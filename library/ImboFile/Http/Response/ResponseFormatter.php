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
     * Content formatters
     *
     * @var array
     */
    private $formatters;

    /**
     * The default mime type to use when formatting a response
     *
     * @var string
     */
    private $defaultMimeType = 'application/json';

    /**
     * Mapping from extensions to mime types
     *
     * @var array
     */
    private $extensionsToMimeType = array(
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
    );

    /**
     * Supported content types and the associated formatter class name or instance, or in the
     * case of an image model, the resulting image type
     *
     * @var array
     */
    private $supportedTypes = array(
        'application/json' => 'json',
        'application/xml'  => 'xml',
        'image/gif'        => 'gif',
        'image/png'        => 'png',
        'image/jpeg'       => 'jpg',
    );

    /**
     * The default types that models support, in a prioritized order
     *
     * @var array
     */
    private $defaultModelTypes = array(
        'application/json',
        'application/xml',
    );

    /**
     * The types the different models can be expressed as, if they don't support the default ones,
     * in a prioritized order. If the user agent sends "Accept: image/*" the first one will be the
     * one used.
     *
     * The keys are the last part of the model name, lowercased:
     *
     * Imbo\Model\Image => image
     * Imbo\Model\FooBar => foobar
     *
     * @var array
     */
    private $modelTypes = array(
        'image' => array(
            'image/jpeg',
            'image/png',
            'image/gif',
        ),
    );

    /**
     * The formatter to use
     *
     * @var string
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return array(
            'response.send' => array('format' => 20),
            'response.negotiate' => 'negotiate',
        );
    }

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
        $event.stopPropagation();
    }
}
