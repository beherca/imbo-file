<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Storage;

use Imbo\Exception\StorageException,
    ImboFile\Model\File;

/**
 * Storage adapter interface
 *
 * This is an interface for storage adapters in Imbo.
 *
 * @author Beherca <beherca@gmail.com>
 * @package Storage
 */
interface StorageInterface {
    /**
     * Store an file
     *
     * This method will receive the binary data of the file and store it somewhere suited for the
     * actual storage adaper. If an error occurs the adapter should throw an
     * Imbo\Exception\StorageException exception.
     *
     * If the file already exists, simply overwrite it.
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier The file identifier
     * @param string $fileData The file data to store
     * @return boolean Returns true on success or false on failure
     * @throws StorageException
     */
    function storeFile($publicKey, $fileIdentifier, $fileData);
    
    /**
     * Delete an file
     *
     * This method will delete the file associated with $fileIdentifier from the storage medium
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return boolean Returns true on success or false on failure
     * @throws StorageException
    */
    function deleteFile($publicKey, $fileIdentifier);
    
    /**
     * Get file content
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return string The binary content of the file
     * @throws StorageException
    */
    function getFile($publicKey, $fileIdentifier);
    
    /**
     * Get the last modified timestamp
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return DateTime Returns an instance of DateTime
     * @throws StorageException
    */
    function getFileLastModified($publicKey, $fileIdentifier);
    
    /**
     * See if the file already exists
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return DateTime Returns an instance of DateTime
     * @throws StorageException
    */
    function fileExists($publicKey, $fileIdentifier);
}
