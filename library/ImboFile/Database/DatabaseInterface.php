<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Database;

use ImboFile\Model\File,
    ImboFile\Resource\Files\Query,
    Imbo\Exception\DatabaseException,
    DateTime;

/**
 * Database adapter interface
 *
 * This is an interface for storage adapters in Imbo.
 *
 * @author Beherca <beherca@gmail.com>
 * @package Database
 */
interface DatabaseInterface {
    /**
     * Insert a new file
     *
     * This method will insert a new file into the database. If the same file already exists,
     * just update the "updated" information.
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @param File $file The file to insert
     * @return boolean Returns true on success or false on failure
     * @throws DatabaseException
     */
    function insertFile($publicKey, $fileIdentifier, File $file);

    /**
     * Delete an file from the database
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return boolean Returns true on success or false on failure
     * @throws DatabaseException
     */
    function deleteFile($publicKey, $fileIdentifier);

    /**
     * Edit metadata
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @param array $metadata An array with metadata
     * @return boolean Returns true on success or false on failure
     * @throws DatabaseException
     */
    function updateFileMetadata($publicKey, $fileIdentifier, array $metadata);

    /**
     * Get all metadata associated with an file
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return array Returns the metadata as an array
     * @throws DatabaseException
     */
    function getFileMetadata($publicKey, $fileIdentifier);

    /**
     * Delete all metadata associated with an file
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier File identifier
     * @return boolean Returns true on success or false on failure
     * @throws DatabaseException
     */
    function deleteFileMetadata($publicKey, $fileIdentifier);

    /**
     * Get files based on some query parameters
     *
     * @param string $publicKey The public key of the user
     * @param Query $query A query instance
     * @return array
     * @throws DatabaseException
     */
    function getFiles($publicKey, Query $query);

    /**
     * Load information from database into the file object
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier The file identifier
     * @param File $file The file object to populate
     * @return boolean
     * @throws DatabaseException
     */
    function loadFile($publicKey, $fileIdentifier, File $file);

    /**
     * Get the last modified timestamp of a user
     *
     * If the $fileIdentifier parameter is set, return when that file was last updated. If not
     * set, return when the user last updated any file. If the user does not have any files
     * stored, return the current timestamp.
     *
     * @param string $publicKey The public key of the user
     * @param string $fileIdentifier The file identifier
     * @return DateTime Returns an instance of DateTime
     * @throws DatabaseException
     */
    function getFileLastModified($publicKey, $fileIdentifier = null);

    /**
     * Get the mime type of an file
     *
     * @param string $publicKey The public key of the user who owns the file
     * @param string $fileIdentifier The file identifier
     * @return string Returns the mime type of the file
     * @throws DatabaseException
     */
    function getFileMimeType($publicKey, $fileIdentifier);

    /**
     * Check if an file already exists
     *
     * @param string $publicKey The public key of the user who owns the file
     * @param string $fileIdentifier The file identifier
     * @return boolean Returns true of the file exists, false otherwise
     * @throws DatabaseException
     */
    function fileExists($publicKey, $fileIdentifier);

    /**
     * Insert a short URL
     *
     * @param string $shortUrlId The ID of the URL
     * @param string $publicKey The public key attached to the URL
     * @param string $fileIdentifier The file identifier attached to the URL
     * @param string $extension Optionl file extension
     * @param array $query Optional query parameters
     * @return boolean
     */
    function insertFileShortUrl($shortUrlId, $publicKey, $fileIdentifier, $extension = null, array $query = array());

    /**
     * Fetch the short URL identifier
     *
     * @param string $publicKey The public key attached to the URL
     * @param string $fileIdentifier The file identifier attached to the URL
     * @param string $extension Optionl file extension
     * @param array $query Optional query parameters
     * @return string|null
     */
    function getFileShortUrlId($publicKey, $fileIdentifier, $extension = null, array $query = array());

    /**
     * Fetch parameters for a short URL
     *
     * @param string $shortUrlId The ID of the short URL
     * @return array|null Returns an array with information regarding the short URL, or null if the
     *                    short URL is not found
     */
    function getFileShortUrlParams($shortUrlId);

    /**
     * Delete short URLs attached to a specific file
     *
     * @param string $publicKey The public key attached to the URL
     * @param string $fileIdentifier The file identifier attached to the URL
     * @return boolean
     */
    function deleteFileShortUrls($publicKey, $fileIdentifier);
}
