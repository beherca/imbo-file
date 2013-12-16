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

use Imbo;
use ImboFile\Model\File,
    Imbo\Exception\DatabaseException,
    Imbo\Exception\InvalidArgumentException,
    Imbo\Exception,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager,
    Doctrine\DBAL\Connection,
    PDO,
    DateTime,
    DateTimeZone;

/**
 * Doctrine 2 database driver
 *
 * Parameters for this driver:
 *
 * - <pre>(string) dbname</pre> Name of the database to connect to
 * - <pre>(string) user</pre> Username to use when connecting
 * - <pre>(string) password</pre> Password to use when connecting
 * - <pre>(string) host</pre> Hostname to use when connecting
 * - <pre>(string) driver</pre> Which driver to use
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @author Beherca <beherca@gmail.com>
 * @package Database
 */
class Doctrine extends Imbo\Database\Doctrine{
    /**
     * Extra table names for the database
     *
     * @var array
     */
    private $fileTableNames = array(
        'fileinfo' => 'fileinfo',
        'filemetadata'  => 'filemetadata',
    );

    /**
     * Class constructor
     *
     * @param array $params Parameters for the driver
     * @param Connection $connection Optional connection instance. Primarily used for testing
     */
    public function __construct(array $params, Connection $connection = null) {
        //add new tables
        $this->tableName = array_merge($this->tableNames, $this->fileTableNames);
        parent::__construct($params, $connection);
    }
    
    

}
