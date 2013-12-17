<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 * (c) Beherca <beherca@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Storage;

use ImboFile\Model\File,
    Imbo\Storage\GridFS as ImboGridFS,
    Imbo\Exception\StorageException,
    Mongo,
    MongoClient,
    MongoGridFS,
    MongoException,
    DateTime,
    DateTimeZone;

/**
 * GridFS (MongoDB) database driver
 *
 * A GridFS storage driver for Imbo
 *
 * Valid parameters for this driver:
 *
 * - <pre>(string) databaseName</pre> Name of the database. Defaults to 'imbo_storage'
 * - <pre>(string) server</pre> The server string to use when connecting to MongoDB. Defaults to
 *                              'mongodb://localhost:27017'
 * - <pre>(array) options</pre> Options to use when creating the Mongo client instance. Defaults to
 *                              array('connect' => true, 'connectTimeoutMS' => 1000).
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @author Beherca <beherca@gmail.com>
 * @package Storage
 */
class GridFS extends ImboGridFS {
    //TODO
}
