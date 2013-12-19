<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\File;

use Imbo\EventManager\EventInterface,
    Imbo\EventListener\ListenerInterface,
    ImboFile\Exception\FileException,
    ImboFile\Model\File,
    Imbo\Exception,
    finfo;

/**
 * File preparation
 *
 * @author Beherca <beherca@gmail.com>
 * @package File
 */
class FilePreparation implements ListenerInterface {
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array(
            'file.put' => array('prepareFile' => 50),
        );
    }

    /**
     * Prepare an file
     *
     * This method should prepare an file object from php://input. The method must also figure out
     * the width, height, mime type and extension of the file.
     *
     * @param EventInterface $event The current event
     * @throws Exception
     */
    public function prepareFile(EventInterface $event) {
        $request = $event->getRequest();

        // Fetch file data from input
        $fileBlob = $request->getContent();

        if (empty($fileBlob)) {
            $e = new FileException('No file attached', 400);
            $e->setImboErrorCode(Exception::IMAGE_NO_IMAGE_ATTACHED);

            throw $e;
        }

        // Calculate hash
        $actualHash = md5($fileBlob);

        // Get file identifier from request
        $fileIdentifier = $request->getFileIdentifier();

        if ($actualHash !== $fileIdentifier) {
            $e = new FileException('Hash mismatch', 400);
            $e->setImboErrorCode(Exception::IMAGE_HASH_MISMATCH);

            throw $e;
        }

        // Use the file info extension to fetch the mime type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($fileBlob);

        if (!File::supportedMimeType($mime)) {
            $e = new FileException('Unsupported file type: ' . $mime, 415);
            $e->setImboErrorCode(Exception::IMAGE_UNSUPPORTED_MIMETYPE);

            throw $e;
        }

        $extension = File::getFileExtension($mime);

        // Store relevant information in the file instance and attach it to the request
        $file = new File();
        $file->setMimeType($mime)
              ->setExtension($extension)
              ->setBlob($fileBlob);

        $request->setFile($file);
    }
}
