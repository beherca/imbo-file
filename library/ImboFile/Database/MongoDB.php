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

namespace ImboFile\Database;

use ImboFile\Model\File,
    Imbo\Database\MongoDB as ImboMongoDB,
    Imbo\Database\DatabaseInterface,
    Imbo\Resource\Images\Query,
    Imbo\Exception\DatabaseException,
    MongoClient,
    MongoCollection,
    MongoException,
    DateTime,
    DateTimeZone;

/**
 * MongoDB database driver
 *
 * A MongoDB database driver for Imbo
 *
 * Valid parameters for this driver:
 *
 * - (string) databaseName Name of the database. Defaults to 'imbo'
 * - (string) server The server string to use when connecting to MongoDB. Defaults to
 *                   'mongodb://localhost:27017'
 * - (array) options Options to use when creating the MongoClient instance. Defaults to
 *                   array('connect' => true, 'connectTimeoutMS' => 1000).
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @author Beherca <beherca@gmail.com>
 * @package Database
 */
class MongoDB extends ImboMongoDB {
  
}
