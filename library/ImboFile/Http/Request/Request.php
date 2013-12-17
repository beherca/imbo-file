<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Http\Request;

use ImboFile\Model\File,
    Imbo\Exception\InvalidArgumentException,
    Imbo\Router\Route,
    Imbo\Http\Request\Request as ImboRequest;

/**
 * Request class
 *
 * @author Beherca <beherca@gmail.com>
 * @package Http
 */
class Request extends ImboRequest {

    /**
     * File instance
     *
     * @var File
     */
    private $file;

    /**
     * Set an file model
     *
     * @param File $file An file model instance
     * @return Request
     */
    public function setFile(File $file) {
        $this->file = $file;

        return $this;
    }

    /**
     * Get an file model attached to the request (on PUT)
     *
     * @return nullFilee
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Get the public key found in the request
     *
     * @return string
     */
    public function getPublicKey() {
        return $this->route ? $this->route->get('publicKey') : null;
    }

    /**
     * Get the private key
     *
     * The private key property is populated by the server based on the public key from the
     * request. The client itself does not place the private key in the request.
     *
     * @return string
     */
    public function getPrivateKey() {
        return $this->privateKey;
    }

    /**
     * Set the private key
     *
     * @param string $key The key to set
     * @return Request
     */
    public function setPrivateKey($key) {
        $this->privateKey = $key;

        return $this;
    }

    /**
     * Get the file identifier from the URL
     *
     * @return string|null
     */
    public function getFileIdentifier() {
        return $this->route ? $this->route->get('fileIdentifier') : null;
    }
}
