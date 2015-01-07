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

define('CONFIG_NAME', 'localhost');

require 'vendor/autoload.php';

class TableNameContainer extends \wplibs\dbinterface\aContainer {

    const OBJECT_NAME = 'TableClassName';
    const TABLE_NAME  = TableClassName::TABLE_NAME;
    
    private static $instances   = null;
    protected      $basicFields = [ 'id' ];
     
     use \wplibs\traits\tGetNamedInstance;
     
     protected function __construct( $name ) {
        parent::__construct( $name );
        self::$instances[ $name ] = $this;
     }

    public function createNew(  ) {
        $obj = parent::createNew();
        /** more code **/
        return $obj;
    }
    
    public function getTableNameByID( $id, \wplibs\database\FieldSelection $selector = null ) {
        $sql = $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'id', '=', (int)$id )->limit( 1 );
        $params = $sql->getQueryParams();
     
        /** make ONE object **/
        $result = $this->makePreparedObject( $sql, self::OBJECT_NAME, ...$params );
     
        return $result;
   }
}

class TableClassName extends \wplibs\dbinterface\aObject implements \wplibs\dbinterface\iCachable {
    
    const TABLE_NAME     = 'TableName';
    const CACHE_TYPE     = 'CacheIdentifierName';
    
    protected $primaryKeys = [ 'id' ];
    protected $hiddenFields = [ ];
  
    protected $loaded       = false;
    
    public static function Factory( array $row, \wplibs\database\iDatabase\iDatabase $db ) {
        if ( $row === null ) {
            return null;
        }
  
        $obj = new self( $row, $db );
  
        return $obj;
    }
}

/** Initialize Config once **/
$conf = \wplibs\config\Config::getNamedInstance( CONFIG_NAME );

/** Get the container **/
$cc = TableNameContainer::getNamedInstance( CONFIG_NAME );

/** Create a new object based on the table **/
$newEntity = $cc->createNew();

/** Get one object (aka row) with id 1 **/
$entityWithIDOne = $cc->getTableNameByID( 1 ); 

/** ... **/

```

## Creating a proper config file

Create a new file in src/configfiles/, for example localhost.config.php,

``` php
<?php
exit;
?>
[DATABASE]
server="localhost"
username="databaseusername"
password="databaseuserpass"
dbname="databasename"
port=3306
dbbackend="mysql"
debugsql=1
[CONFIG]
debug=1
cache=1
debuglog=1
server_name=localhost

```

## Why use ORMBASE?

It's simple, easy-to-use and extremely small with a huge amount of components.

## Who uses ORMBASE?

Me ;-)


## License

Proprietary

Copyright (c) 2015 Christian Senkowski <cs@e-cs.co>

You may use the classest for testing and personal use only.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.