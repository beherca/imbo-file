<?php
/**
 * This file is part of the ImboFile package
 *
 * (c) Kai Li <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Resource;

use Imbo\Exception\ResourceException,
    Imbo\EventManager\EventInterface,
    Imbo\Model;

/**
 * File resource
 *
 * @author Kai Li <beherca@gmail.com>
 * @package Resources
 */
class File implements ResourceInterface {
    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods() {
        return array('GET', 'HEAD', 'DELETE', 'PUT');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array(
            'file.get' => 'get',
            'file.head' => 'get',
            'file.delete' => 'delete',
            'file.put' => 'put',
        );
    }

    /**
     * Handle PUT requests
     *
     * @param EventInterface
     */
    public function put(EventInterface $event) {
        $event->getManager()->trigger('db.file.insert');
        $event->getManager()->trigger('storage.file.insert');

        $request = $event->getRequest();
        $response = $event->getResponse();
        $file = $request->getFile();

        $model = new Model\ArrayModel();
        $model->setData(array(
            'fileIdentifier' => $file->getChecksum(),
            'extension' => $file->getExtension(),
        ));

        $response->setModel($model);
    }

    /**
     * Handle DELETE requests
     *
     * @param EventInterface
     */
    public function delete(EventInterface $event) {
        $event->getManager()->trigger('db.file.delete');
        $event->getManager()->trigger('storage.file.delete');

        $model = new Model\ArrayModel();
        $model->setData(array(
            'fileIdentifier' => $event->getRequest()->getImageIdentifier(),
        ));

        $event->getResponse()->setModel($model);
    }

    /**
     * Handle GET requests
     *
     * @param EventInterface
     */
    public function get(EventInterface $event) {
        printf('ok--------------');
        $request = $event->getRequest();
        $response = $event->getResponse();
        $eventManager = $event->getManager();

        $publicKey = $request->getPublicKey();
        $fileIdentifier = $request->getFileIdentifier();

        $file = new Model\File();
        $file->setFileIdentifier($fileIdentifier)
              ->setPublicKey($publicKey);

        $response->setModel($file);

        $eventManager->trigger('db.file.load');
        $eventManager->trigger('storage.file.load');

        // Custom Imbo headers
        $response->headers->add(array(
            'X-Imbo-OriginalMimeType' => $file->getMimeType(),
            'X-Imbo-OriginalFileSize' => $file->getFilesize(),
            'X-Imbo-OriginalExtension' => $file->getExtension(),
        ));

        // Trigger possible image transformations
        $eventManager->trigger('file.compress');
    }
}
