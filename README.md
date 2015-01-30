# ORMBASE - Simple ORM Basic Libraries

A project written by [Christian Senkowski](http://e-cs.co/).

## About

ORMBASE is an easy-to-use [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
compliant class set for PHP. It is designed as basic for [ORM](http://en.wikipedia.org/wiki/Object-relational_mapping) based projects 
and comes with some examples, caching and a Database-Abstract-Layer which provides access to (for now) Mysql and MongoDB.

## Installation

### Composer

From the Command Line:

```
composer require ecsco/ormbase:dev-master
```

In your `composer.json`:

``` json
{
    "require": {
        "ecsco/ormbase": "dev-master"
    }
}
```

## Basic Usage

``` php
<?php

require 'vendor/autoload.php';

/** Init config **/
$config = \ecsco\ormbase\config\Config::getInstance();

$config->addItem('database', 'server', 'localhost');
$config->addItem('database', 'port', '3306');
$config->addItem('database', 'username', 'my_user');
$config->addItem('database', 'password', 'my_pass');
$config->addItem('database', 'dbname', 'server_v4');
$config->addItem('database', 'databaseclass', '\ecsco\ormbase\database\mysql\Database');
$config->addItem('database', 'debugsql', '1');

/** If memcached available **/
$config->addItem('cache', 'cacheclass', '\ecsco\ormbase\cache\memcached\Cache');
$config->addItem('cache', 'server', '127.0.0.1');
$config->addItem('cache', 'port', '11211');

class TableNameContainer extends \ecsco\ormbase\AbstractContainer {

    const OBJECT_NAME = 'TableClassName';
    const TABLE_NAME  = TableClassName::TABLE_NAME;
    
    protected      $basicFields = [ 'id' ];
     
    use \ecsco\ormbase\traits\SingletonTrait;
     
    public function createNew(  ) {
        $obj = parent::createNew();
        /** more code **/
        return $obj;
    }
    
    public function getTableNameByID( $id, \ecsco\ormbase\database\FieldSelection $selector = null ) {
        $sql = $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'id', '=', (int)$id )->limit( 1 );
        $params = $sql->getQueryParams();
     
        /** make ONE object **/
        $result = $this->makePreparedObject( $sql, self::OBJECT_NAME, ...$params );
     
        return $result;
   }
}

class TableClassName extends \ecsco\ormbase\AbstractObject implements \ecsco\ormbase\CachableInterface {
    
    const TABLE_NAME     = 'TableName';
    
    protected $primaryKeys = [ 'id' ];
    protected $hiddenFields = [ ];
    
    public static function getCacheIdentifier() {
        return 'cacheNameForThisEntity';
    }
}

/** Clear first. Just for demonstrating how to get a database-connection **/
$db = \ecsco\ormbase\database\DatabaseAccess::getDatabaseInstance( $config );
$db->query ( $db->delete()->from( \Customer::TABLE_NAME ) );

/** Get the container **/
$cc = TableNameContainer::getInstance();

/** Create a new object based on the table **/
$newEntity = $cc->createNew();
$newEntity->setValue('keyname1', 'value1');
$newEntity->setValue('keyname2', 'value2');
/** .... */
$newEntity->store();

/** Get one object (aka row) with id 1 **/
$entityWithIDOne = $cc->getTableNameByID( 1 ); 
$entityWithIDOne->setValue('key_name_in_table1', 'foobar');
$entityWithIDOne->store();

print_r( $entityWithIDOne->toArray() );

/** ... **/

```

## Why use ORMBASE?

It's simple, easy-to-use and extremely small with a huge amount of components.

## Who uses ORMBASE?

Me ;-)


## License

Proprietary

Copyright (c) 2015 Christian Senkowski <cs@e-cs.co>

You may use the classes for testing and personal use only.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
