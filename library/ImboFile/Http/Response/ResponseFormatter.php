<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Http\Response;

use Imbo\EventManager\EventInterface,
    Imbo\Http\Response\ResponseFormatter as ImboResponseFormatter,
    Imbo\EventListener\ListenerInterface,
    Imbo\Model as ImboModel,
    Imbo\Exception,
    Imbo\Http\ContentNegotiation,
    ImboFile\Model, 
    Symfony\Component\HttpFoundation\AcceptHeader;

/**
 * This event listener will correctly format the response body based on the Accept headers in the
 * request
 *
 * @author Beherca <beherca@gmail.com> 
 * @package Http
 */
class ResponseFormatter extends ImboResponseFormatter {

    /**
     * Response send hook
     *
     * @param EventInterface $event The current event
     */
    public function format(EventInterface $event) {
        $response = $event->getResponse();
        $model = $response->getModel();

        if ($response->getStatusCode() === 204 || !$model) {
            // No content to write
            return;
        }

        $request = $event->getRequest();

        // If we are dealing with an image we want to trigger an event that handles a possible
        // conversion
        if ($model instanceof ImboModel\Image) {
            $eventManager = $event->getManager();

            if ($this->extensionsToMimeType[$this->formatter] !== $model->getMimeType()) {
                $eventManager->trigger('image.transformation.convert', array(
                    'image' => $model,
                    'params' => array(
                        'type' => $this->formatter,
                    ),
                ));
            }

            // Finished transforming the image
            $eventManager->trigger('image.transformed', array('image' => $model));

            $formattedData = $model->getBlob();
            $contentType = $model->getMimeType();
        } else if($model instanceof Model\File){
          $eventManager = $event->getManager();
          
          // Finished compress file
          $eventManager->trigger('file.compress', array('file' => $model));
          
          $formattedData = $model->getBlob();
          $contentType = $model->getMimeType();
        } else { //for images and files information
            // Create an instance of the formatter
            $formatter = $this->formatters[$this->formatter];

            $formattedData = $formatter->format($model);
            $contentType = $formatter->getContentType();
        }

        if ($contentType === 'application/json') {
            foreach (array('callback', 'jsonp', 'json') as $validParam) {
                if ($request->query->has($validParam)) {
                    $formattedData = sprintf("%s(%s)", $request->query->get($validParam), $formattedData);
                    break;
                }
            }
        }

        $response->headers->add(array(
            'Content-Type' => $contentType,
            'Content-Length' => strlen($formattedData),
        ));

        if ($request->getMethod() !== 'HEAD') {
            $response->setContent($formattedData);
        }
        
        //Prevent Default Formatter
        $event->stopPropagation();
    }
}
