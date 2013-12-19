<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace Imbo;

use ImboFile\Database\Doctrine,
    ImboFile\Database\MongoDB,
    ImboFile\Storage\GridFs,
    ImboFile\Http\Request;

$config = array(
    /**
     * Database adapter
     *
     * See the different adapter implementations for possible configuration parameters. The value
     * must be set to a closure returning an instance of Imbo\Database\DatabaseInterface, or an
     * implementation of said interface.
     *
     * @var Imbo\Database\DatabaseInterface|Closure
     */
    'database' => function() {
        return new MongoDB();
    },

    /**
     * Storage adapter
     *
     * See the different adapter implementations for possible configuration parameters. The value
     * must be set to a closure returning an instance of Imbo\Storage\StorageInterface, or an
     * implementation of said interface.
     *
     * @var Imbo\Storage\StorageInterface|Closure
     */
    'storage' => function() {
        return new GridFS();
    },
    
    /**
     * Custom request
     *
     * You can override default imbo request here
     *
     * @var Imbo\Request
     */
    'request' => function() {
      return new Request();
    },

    /**
     * Event listeners
     *
     * An associative array where the keys are short names for the event listeners (not really used
     * for anything, but exists so you can override/unset some helpers from config.php). The values
     * of each element in this array can be one of the following:
     *
     * 1) A string representing a class name of a class implementing the
     *    Imbo\EventListener\ListenerInteface interface
     *
     * 2) An instance of an object implementing the Imbo\EventListener\ListenerInterface interface
     *
     * 3) A closure returning an instance of an object implementing the
     *    Imbo\EventListener\ListenerInterface interface
     *
     * 4) An array with the following keys:
     *
     *   - listener (required)
     *   - params
     *   - publicKeys
     *
     *   where 'listener' is one of the following:
     *
     *     1) a string representing a class name of a class implementing the
     *        Imbo\EventListener\ListenerInterface interface
     *
     *     2) an instance of the Imbo\EventListener\ListenerInterface interface
     *
     *     3) a closure returning an instance Imbo\EventListener\ListenerInterface
     *
     *   'params' is an array with parameters for the constructor of the event listener. This is
     *   only used when the 'listener' key is a string containing a class name.
     *
     *   'publicKeys' is an array with one of the following keys:
     *
     *     - whitelist
     *     - blacklist
     *
     *     where 'whitelist' is an array of public keys that the listener *will* trigger for, and
     *     'blacklist' is an array of public keys that the listener *will not* trigger for.
     *
     * 5) An array with the following keys:
     *
     *   - events (required)
     *   - callback (required)
     *   - priority
     *   - publicKeys
     *
     *   where 'events' is an array of events that 'callback' will subscribe to. If your callback
     *   subscribes to several events, and you want to use different priorities for the events,
     *   simply specify an associative array where the keys are the event names, and the values are
     *   the priorities for each event. If you use this method, the 'priority' key will be ignored.
     *
     *   'callback' is any callable function. The function will receive a single argument, which is
     *   an instance of Imbo\EventManager\EventInterface.
     *
     *   'priority' is the priority of your callback. This defaults to 0 (low priority). The
     *   priority can also be a negative number if you want your listeners to be triggered after
     *   Imbo's event listeners.
     *
     *   'publicKeys' is the same as described above.
     *
     * Examples of how to add listeners:
     *
     * 'eventListeners' => array(
     *   // 1) A class name in a string
     *   'accessToken' => 'Imbo\EventListener\ListenerInterface',
     *
     *   // 2) Implementation of a listener interface
     *   'auth' => new EventListener\Authenticate(),
     *
     *   // 3) Implementation of a listener interface with a public key filter
     *   'maxImageSize' => array(
     *     'listener' => EventListener\MaxImageSize(1024, 768),
     *     'publicKeys' => array(
     *       'whitelist' => array( ... ),
     *       // 'blacklist' => array( ... ),
     *       )
     *     )
     *   ),
     *
     *   // 4) A class name in a string with custom parameters for the listener
     *   'statsAccess' => array(
     *       'listener' => 'Imbo\EventListener\StatsAccess',
     *       'params' => array(
     *           array(
     *               'whitelist' => array('127.0.0.1', '::1'),
     *               'blacklist' => array(),
     *           )
     *       ),
     *   ),
     *
     *   // 5) A closure that will subscribe to two events with different priorities
     *   'anotherCustomCallback' => array(
     *       'callback' => function($event) {
     *           // Some code
     *       },
     *       'events' => array(
     *           'image.get' => 20, // Trigger BEFORE the internal handler for "image.get"
     *           'image.post' => -20, // Trigger AFTER the internal handler for "image.post"
     *       ),
     *   ),
     *
     * @var array
     */
    'eventListeners' => array(
        //'file.compress' => 'ImboFile\EventListener\CompressFile',
        //use the same name to override default formatter
        'Imbo\Http\Response\ResponseFormatter' => array(
            'listener' => 'ImboFile\Http\Response\ResponseFormatter',
            'params' => array(
                array(
                    'json' => new \ImboFile\Http\Response\Formatter\JSON(new Helpers\DateFormatter()),
                    'xml'  => new \ImboFile\Http\Response\Formatter\XML(new Helpers\DateFormatter()),
                ),
                new Http\ContentNegotiation(),
            ),
            'priority' => 1,
        ),
        'Imbo\EventListener\StorageOperations' => array(
            'listener' => 'ImboFile\EventListener\StorageOperations',
            'priority' => 1,
        ),
        'Imbo\EventListener\DatabaseOperations' => array(
            'listener' => 'ImboFile\EventListener\DatabaseOperations',
            'priority' => 1,
        ),
        'Imbo\EventListener\ResponseSender' => 'ImboFile\EventListener\ResponseSender',
        'file.put' => 'ImboFile\File\FilePreparation',
    ),

    /**
     * Custom resources for Imbo
     *
     * @link http://docs.imbo-project.org
     * @var array
     */
    'resources' => array(
        'file' => 'ImboFile\Resource\File',
    ),

    /**
     * Custom routes for Imbo
     *
     * @link http://docs.imbo-project.org
     * @var array
     */
    'routes' => array(
        'file'    => '#^/users/(?<publicKey>[a-z0-9_-]{3,})/files/(?<fileIdentifier>[a-f0-9]{32})(\.(?<extension>doc|excel|txt|zip|rar|pdf|zip|ppt|csv|mp4|jpg|jpeg|png|gif))?$#',
        'files'   => '#^/users/(?<publicKey>[a-z0-9_-]{3,})/files(/|(\.(?<extension>json|xml)))?$#',
        'filemetadata' => '#^/users/(?<publicKey>[a-z0-9_-]{3,})/files/(?<fileIdentifier>[a-f0-9]{32})/meta(?:data)?(/|\.(?<extension>json|xml))?$#',
    ),
);
return $config;