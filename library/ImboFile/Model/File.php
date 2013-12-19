<?php
/**
 * This file is part of the ImboFile package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Model;

use Imbo\Model\ModelInterface,
    DateTime;

/**
 * File model
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @author Beherca <beherca@gmail.com>
 * @package Models
 */
class File implements ModelInterface {
    /**
     * Supported mime types and the correct file extensions
     *
     * @var array
     */
    static public $mimeTypes = array(
        'application/vnd.ms-excel'  => 'ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'ms-excel-2007',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'ppt-2007',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/pdf' => 'pdf',
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'text/csv'  => 'csv',
        'text/plain' => 'txt',
        'application/pdf' => 'pdf',
        'application/zip' => 'zip',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpeg',
        'image/png' => 'png',
        'audio/mp4' => 'mp4',
    );

    /**
     * Size of the file
     *
     * @var int
     */
    private $filesize;

    /**
     * Mime type of the file
     *
     * @var string
     */
    private $mimeType;

    /**
     * Extension of the file without the dot
     *
     * @var string
     */
    private $extension;

    /**
     * Blob containing the file itself
     *
     * @var string
     */
    private $blob;

    /**
     * The metadata attached to this file
     *
     * @var array
     */
    private $metadata;

    /**
     * MD5 checksum of the file data
     *
     * @var string
     */
    private $checksum;

    /**
     * Added date
     *
     * @var DateTime
     */
    private $added;

    /**
     * Updated date
     *
     * @var DateTime
     */
    private $updated;

    /**
     * Public key
     *
     * @var string
     */
    private $publicKey;

    /**
     * file identifier
     *
     * @var string
     */
    private $fileIdentifier;

    /**
     * Flag informing us if the file has been transformed by any file transformations
     *
     * @var boolean
     */
    private $hasBeenTransformed = false;

    /**
     * Get the size of the file data in bytes
     *
     * @return int
     */
    public function getFilesize() {
        return $this->filesize;
    }

    /**
     * Set the size of the file in bytes
     *
     * @param int $size The size of the file
     * @return File
     */
    public function setFilesize($size) {
        $this->filesize = (int) $size;

        return $this;
    }

    /**
     * Get the mime type
     *
     * @return string
     */
    public function getMimeType() {
        return $this->mimeType;
    }

    /**
     * Set the mime type
     *
     * @param string $mimeType The mime type, for instance "text/plain"
     * @return File
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get the extension
     *
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * Set the extension
     *
     * @param string $extension The file extension
     * @return File
     */
    public function setExtension($extension) {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the blob
     *
     * @return string
     */
    public function getBlob() {
        return $this->blob;
    }

    /**
     * Set the blob and update filesize and checksum properties
     *
     * @param string $blob The binary data to set
     * @return File
     */
    public function setBlob($blob) {
        $this->blob = $blob;
        $this->setFilesize(strlen($blob));
        $this->setChecksum(md5($blob));

        return $this;
    }

    /**
     * Get the metadata
     *
     * @return array
     */
    public function getMetadata() {
        return $this->metadata;
    }

    /**
     * Set the metadata
     *
     * @param array $metadata An array with metadata
     * @return File
     */
    public function setMetadata(array $metadata) {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return DateTime
     */
    public function getAddedDate() {
        return $this->added;
    }

    /**
     * Set the added date
     *
     * @param DateTime $added When the File was added
     * @return File
     */
    public function setAddedDate(DateTime $added) {
        $this->added = $added;

        return $this;
    }

    /**
     * Get the updated date
     *
     * @return DateTime
     */
    public function getUpdatedDate() {
        return $this->updated;
    }

    /**
     * Set the updated date
     *
     * @param DateTime $updated When the File was updated
     * @return File
     */
    public function setUpdatedDate(DateTime $updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get the public key
     *
     * @return string
     */
    public function getPublicKey() {
        return $this->publicKey;
    }

    /**
     * Set the public key
     *
     * @param string $publicKey The public key
     * @return File
     */
    public function setPublicKey($publicKey) {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get the File identifier
     *
     * @return string
     */
    public function getFileIdentifier() {
        return $this->fileIdentifier;
    }

    /**
     * Set the public key
     *
     * @param string $fileIdentifier The public key
     * @return File
     */
    public function setFileIdentifier($fileIdentifier) {
        $this->fileIdentifier = $fileIdentifier;

        return $this;
    }

    /**
     * Get the checksum of the current file data
     *
     * @return string
     */
    public function getChecksum() {
        return $this->checksum;
    }

    /**
     * Set the checksum
     *
     * @param string $checksum The checksum to set
     * @return File
     */
    public function setChecksum($checksum) {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Set or get the hasBeenTransformed flag
     *
     * @param boolean|null $flag Skip the argument to get the current value
     * @return boolean|self
     */
    public function hasBeenTransformed($flag = null) {
        if ($flag === null) {
            return $this->hasBeenTransformed;
        }

        $this->hasBeenTransformed = (bool) $flag;

        return $this;
    }

    /**
     * Check if a mime type is supported by Imbo
     *
     * @param string $mime The mime type to check. For instance "text/plain"
     * @return boolean
     */
    static public function supportedMimeType($mime) {
        return isset(self::$mimeTypes[$mime]);
    }

    /**
     * Get the file extension mapped to a mime type
     *
     * @param string $mime The mime type. For instance "text/plain"
     * @return boolean|string The extension (without the leading dot) on success or boolean false
     *                        if the mime type is not supported.
     */
    static public function getFileExtension($mime) {
        return isset(self::$mimeTypes[$mime]) ? self::$mimeTypes[$mime] : false;
    }
}
