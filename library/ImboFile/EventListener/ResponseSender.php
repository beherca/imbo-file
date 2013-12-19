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
    Imbo\EventListener\ListenerInterface;

/**
 * Response sender listener
 *
 * @author Beherca <beherca@gmail.com>
 * @package Event\Listeners
 */
class ResponseSender implements ListenerInterface {
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array(
            'response.send' => 'send',
        );
    }

    /**
     * Send the response
     *
     * @param EventInterface $event The current event
     */
    public function send(EventInterface $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Optionally mark this response as not modified
        $response->isNotModified($request);

        // Inject a possible image identifier into the response headers
        $imageIdentifier = null;

        $fileIdentifier = null;
        if ($image = $request->getImage()) {
            // The request has an image. This means that an image was just added. Use the image's
            // checksum
            $imageIdentifier = $image->getChecksum();
        } else if ($identifier = $request->getImageIdentifier()) {
            // An image identifier exists in the request, use that one (and not a possible image
            // checksum for an image attached to the response)
            $imageIdentifier = $identifier;
        }else if($file = $request->getFile()) {
            // The request has an file. This means that an file was just added. Use the file's
            // checksum
            $fileIdentifier = $file->getChecksum();
        } else if ($identifier = $request->getFileIdentifier()) {
            // An file identifier exists in the request, use that one (and not a possible file
            // checksum for an file attached to the response)
            $fileIdentifier = $identifier;
        }

        if ($imageIdentifier) {
            $response->headers->set('X-Imbo-ImageIdentifier', $imageIdentifier);
        }
        
        if ($fileIdentifier) {
          $response->headers->set('X-Imbo-FileIdentifier', $fileIdentifier);
        }

        $response->send();
    }
}
