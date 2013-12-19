<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\EventListener;

use Imbo\EventListener\StorageOperations as ImboStorageOperations,
    Imbo\EventManager\EventInterface;

/**
 * Storage operations event listener
 *
 * @author Beherca <beherca@gmail.com>
 * @package Event\Listeners
 */
class StorageOperations extends ImboStorageOperations{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array_merge(array(
            'storage.file.delete' => 'deleteFile',
            'storage.file.load' => 'loadFile',
            'storage.file.insert' => 'insertFile',
        ), ImboStorageOperations::getSubscribedEvents());
    }

    /**
     * Delete an file
     *
     * @param EventInterface $event An event instance
     */
    public function deleteFile(EventInterface $event) {
        $request = $event->getRequest();
        $event->getStorage()->deleteFile($request->getPublicKey(), $request->getFileIdentifier());
    }

    /**
     * Load an file
     *
     * @param EventInterface $event An event instance
     */
    public function loadFile(EventInterface $event) {
        $storage = $event->getStorage();
        $request = $event->getRequest();
        $response = $event->getResponse();
        $publicKey = $request->getPublicKey();
        $fileIdentifier = $request->getFileIdentifier();

        $fileData = $storage->getFile($publicKey, $fileIdentifier);
        $lastModified = $storage->getFileLastModified($publicKey, $fileIdentifier);

        $response->setLastModified($lastModified)
                 ->getModel()->setBlob($fileData);
    }

    /**
     * Insert an file
     *
     * @param EventInterface $event An event instance
     */
    public function insertFile(EventInterface $event) {
        $request = $event->getRequest();
        $publicKey = $request->getPublicKey();
        $file = $request->getFile();
        $fileIdentifier = $file->getChecksum();
        $blob = $file->getBlob();

        try {
            $exists = $event->getStorage()->fileExists($publicKey, $fileIdentifier);
            $event->getStorage()->storeFile(
                $publicKey,
                $fileIdentifier,
                $blob
            );
        } catch (StorageException $e) {
            $event->getDatabase()->deleteFile(
                $publicKey,
                $fileIdentifier
            );

            throw $e;
        }

        $event->getResponse()->setStatusCode($exists ? 200 : 201);
    }
}
