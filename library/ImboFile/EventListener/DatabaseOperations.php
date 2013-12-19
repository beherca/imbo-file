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

use Imbo\EventManager\EventInterface,
    Imbo\Database\DatabaseInterface,
    Imbo\EventListener\DatabaseOperations as ImboDatabaseOperations,
    Imbo\Resource\Files\Query as FilesQuery,
    Imbo\Model,
    DateTime;

/**
 * Database operations event listener
 *
 * @author Beherca <beherca@gmail.com>
 * @package Event\Listeners
 */
class DatabaseOperations  extends ImboDatabaseOperations {
    /**
     * An files query object
     *
     * @var FilesQuery
     */
    private $filesQuery;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array_merge(array(
            'db.file.insert'    => 'insertFile',
            'db.file.delete'    => 'deleteFile',
            'db.file.load'      => 'loadFile',
            'db.files.load'     => 'loadFiles',
            'db.filemetadata.delete' => 'deleteFileMetadata',
            'db.filemetadata.update' => 'updateFileMetadata',
            'db.filemetadata.load'   => 'loadFileMetadata',
        ), ImboDatabaseOperations::getSubscribedEvents());
    }

    /**
     * Set the files query
     *
     * @param FilesQuery $query The query object
     * @return self
     */
    public function setFilesQuery(FilesQuery $query) {
        $this->filesQuery = $query;

        return $this;
    }

    /**
     * Get the files query
     *
     * @return FilesQuery
     */
    public function getFilesQuery() {
        if (!$this->filesQuery) {
            $this->filesQuery = new FilesQuery();
        }

        return $this->filesQuery;
    }

    /**
     * Insert an file
     *
     * @param EventInterface $event An event instance
     */
    public function insertFile(EventInterface $event) {
        $request = $event->getRequest();

        $event->getDatabase()->insertFile(
            $request->getPublicKey(),
            $request->getFile()->getChecksum(),
            $request->getFile()
        );
    }

    /**
     * Delete an file
     *
     * @param EventInterface $event An event instance
     */
    public function deleteFile(EventInterface $event) {
        $request = $event->getRequest();

        $event->getDatabase()->deleteFile(
            $request->getPublicKey(),
            $request->getFileIdentifier()
        );
    }

    /**
     * Load an file
     *
     * @param EventInterface $event An event instance
     */
    public function loadFile(EventInterface $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $event->getDatabase()->load(
            $request->getPublicKey(),
            $request->getFileIdentifier(),
            $response->getModel()
        );
    }

    /**
     * Delete metadata
     *
     * @param EventInterface $event An event instance
     */
    public function deleteFileMetadata(EventInterface $event) {
        $request = $event->getRequest();

        $event->getDatabase()->deleteFileMetadata(
            $request->getPublicKey(),
            $request->getFileIdentifier()
        );
    }

    /**
     * Update metadata
     *
     * @param EventInterface $event An event instance
     */
    public function updateFileMetadata(EventInterface $event) {
        $request = $event->getRequest();

        $event->getDatabase()->updateFileMetadata(
            $request->getPublicKey(),
            $request->getFileIdentifier(),
            $event->getArgument('metadata')
        );
    }

    /**
     * Load metadata
     *
     * @param EventInterface $event An event instance
     */
    public function loadFileMetadata(EventInterface $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $publicKey = $request->getPublicKey();
        $fileIdentifier = $request->getFileIdentifier();
        $database = $event->getDatabase();

        $model = new Model\Metadata();
        $model->setData($database->getMetadata($publicKey, $fileIdentifier));

        $response->setModel($model)
                 ->setLastModified($database->getFileLastModified($publicKey, $fileIdentifier));
    }

    /**
     * Load files
     *
     * @param EventInterface $event An event instance
     */
    public function loadFiles(EventInterface $event) {
        $query = $this->getFilesQuery();
        $params = $event->getRequest()->query;
        $returnMetadata = false;

        if ($params->has('page')) {
            $query->page($params->get('page'));
        }

        if ($params->has('limit')) {
            $query->limit($params->get('limit'));
        }

        if ($params->has('metadata')) {
            $query->returnMetadata($params->get('metadata'));
            $returnMetadata = true;
        }

        if ($params->has('from')) {
            $query->from($params->get('from'));
        }

        if ($params->has('to')) {
            $query->to($params->get('to'));
        }

        if ($params->has('sort')) {
            $query->sort($params->get('sort'));
        }

        if ($params->has('query')) {
            $data = json_decode($params->get('query'), true);

            if (is_array($data)) {
                $query->metadataQuery($data);
            }
        }

        if ($params->has('fileIdentifiers')) {
            $fileIdentifiers = trim($params->get('fileIdentifiers'));

            if (!empty($fileIdentifiers)) {
                $query->fileIdentifiers(explode(',', $fileIdentifiers));
            }
        }

        $publicKey = $event->getRequest()->getPublicKey();
        $response = $event->getResponse();
        $database = $event->getDatabase();

        $files = $database->getFiles($publicKey, $query);
        $modelFiles = array();

        foreach ($files as $file) {
            $entry = new Model\File();
            $entry->setFilesize($file['size'])
                  ->setPublicKey($publicKey)
                  ->setFileIdentifier($file['fileIdentifier'])
                  ->setChecksum($file['checksum'])
                  ->setMimeType($file['mime'])
                  ->setExtension($file['extension'])
                  ->setAddedDate($file['added'])
                  ->setUpdatedDate($file['updated']);

            if ($returnMetadata) {
                $entry->setMetadata($file['metadata']);
            }

            $modelFiles[] = $entry;
        }

        $model = new Model\Files();
        $model->setFiles($modelFiles);

        if ($params->has('fields')) {
            $fields = trim($params->get('fields'));

            if (!empty($fields)) {
                $model->setFields(explode(',', $fields));
            }
        }

        $lastModified = $database->getFileLastModified($publicKey);

        $response->setModel($model)
                 ->setLastModified($lastModified);
    }

    /**
     * Load user data
     *
     * @param EventInterface $event An event instance
     */
    public function loadUser(EventInterface $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $publicKey = $request->getPublicKey();
        $database = $event->getDatabase();

        $numFiles = $database->getNumFiles($publicKey);
        $lastModified = $database->getFileLastModified($publicKey);

        $userModel = new Model\User();
        $userModel->setPublicKey($publicKey)
                  ->setNumFiles($numFiles)
                  ->setLastModified($lastModified);

        $response->setModel($userModel)
                 ->setLastModified($lastModified);
    }

    /**
     * Load stats
     *
     * @param EventInterface $event An event instance
     */
    public function loadStats(EventInterface $event) {
        $response = $event->getResponse();
        $database = $event->getDatabase();
        $publicKeys = array_keys($event->getConfig()['auth']);
        $users = array();

        foreach ($publicKeys as $key) {
            $users[$key] = array(
                'numFiles' => $database->getNumFiles($key),
                'numBytes' => $database->getNumBytes($key),
            );
        }

        $statsModel = new Model\Stats();
        $statsModel->setUsers($users);

        $response->setModel($statsModel);
    }
}
