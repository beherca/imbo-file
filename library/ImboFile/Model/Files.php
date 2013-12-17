<?php
/**
 * This file is part of the ImboFile package
 *
 * (c) Beherca <beherca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Model;

/**
 * Files model
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @author Beherca <beherca@gmail.com>
 * @package Models
 */
class Files implements ModelInterface {
    /**
     * An array of File models
     *
     * @var File[]
     */
    private $files = array();

    /**
     * Which fields to display
     *
     * @var string[]
     */
    private $fields = array();

    /**
     * Set the array of files
     *
     * @param File[] $files An array of File models
     * @return Files
     */
    public function setFiles(array $files) {
        $this->files = $files;

        return $this;
    }

    /**
     * Get the files
     *
     * @return File[]
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * Set the fields to display
     *
     * @param string[]
     * @return self
     */
    public function setFields(array $fields) {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the fields to display
     *
     * @return string[]
     */
    public function getFields() {
        return $this->fields;
    }
}
