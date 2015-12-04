<?php
/**
 * AbstractObject.php
 * @package    ecsco\ormbase
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */

declare(strict_types=1);

namespace ecsco\ormbase;

use ecsco\ormbase\cache\CacheAccess;
use ecsco\ormbase\database\DatabaseInterface;
use ecsco\ormbase\database\DBResultRow;
use ecsco\ormbase\exception\DatabaseException;
use ecsco\ormbase\exception\ObjectException;

/**
 * AbstractObject
 * @package    ecsco\ormbase
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */
abstract class AbstractObject extends DBResultRow {

    /**
     * Standard cache identifier name
     * Should be overwritten by subclasses
     * @var string
     */
    protected static $cacheIdentifier = 'global';

    /**
     * primaryKeys of this row
     * @var array
     */
    protected $primaryKeys = [ ];

    /**
     * Loaded or not
     * @var bool
     */
    protected $loaded = false;

    /**
     * list of fields that wont show up
     * in toArray - method. i.e. to hide password
     * this must be key => null in order to make
     * array_diff_key working correctly
     * @var array
     */
    protected $hiddenFields = [ ];

    /**
     * Static keys/values
     * Those will show up in toArray
     * but never be stored anywhere
     * @var array
     */
    protected $staticRow = [ ];

    /**
     * Construct
     *
     * @param array             $row
     * @param DatabaseInterface $db
     */
    public function __construct( array $row, DatabaseInterface $db ) {

        parent::__construct( $row, $db );
    }

    /**
     * General abstract Factory
     *
     * @param array                                     $row
     * @param \ecsco\ormbase\database\DatabaseInterface $db
     *
     * @return \ecsco\ormbase\AbstractObject
     */
    public static function factory( array $row, DatabaseInterface $db ): AbstractObject {

        $objectName = get_called_class();

        return new $objectName( $row, $db );
    }

    /**
     * Hide all fields of a n object
     * @return void
     */
    public function hideAllFields() {

        $this->hiddenFields = $this->row;
    }

    /**
     * setHiddenFields
     *
     * @param array $hiddenFields
     *
     * @return void
     */
    public function setHiddenFields( array $hiddenFields ) {

        $this->hiddenFields = $hiddenFields;
    }

    /**
     * Hide a specific field from toArray
     *
     * @param $key
     *
     * @return void
     */
    public function hideField( string $key ) {

        if ( $this->hasKey( $key ) && !isset( $this->hiddenFields[ $key ] ) ) {
            $this->hiddenFields[ $key ] = true;
        }
    }

    /**
     * Unhide a specific field
     *
     * @param string
     *
     * @return void
     */
    public function unhideField( string $key ) {

        if ( $this->hasKey( $key ) && isset( $this->hiddenFields[ $key ] ) ) {
            unset( $this->hiddenFields[ $key ] );
        }
    }

    /**
     * Add a static key and value
     * Those will show up in toArray
     * but never be stored anywhere
     *
     * @param $key
     * @param $value
     *
     * @return void
     * @throws ObjectException
     */
    public function setStaticValue( string $key, $value ) {

        if ( $this->hasKey( $key ) ) {
            throw new ObjectException( "Cannot add static field '$key' ($value), table already has it." );
        }

        $this->staticRow[ $key ] = $value;
    }

    /**
     * Load full details
     *
     * @param boolean
     *
     * @return boolean
     */
    public function loadFullDetails( bool $forceReload = false ) {

        if ( ( $this->loaded && !$forceReload ) || $this->new ) {
            return true;
        }

        return true;
    }

    /**
     * Get a value
     *
     * @param string
     *
     * @throws \ecsco\ormbase\exception\ObjectException
     * @return mixed
     */
    final public function getValue( string $key ) {

        try {
            $value = parent::getValue( $key );
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not find key '$key' in actual resultSet for '" .
                                       get_called_class() .
                                       "' " .
                                       var_export( $this->row, true ) );
        }

        return $value;
    }

    /**
     * setValueByObject
     *
     * @param mixed     $key
     * @param \stdClass $obj
     * @param mixed     $alternateObjKey
     * @param mixed     $defaultValue
     *
     * @throws \ecsco\ormbase\exception\ObjectException
     * @return void
     */
    final public function setValueByObject( string $key, \stdClass $obj, $alternateObjKey = null, $defaultValue = null ) {

        $objKey = ( $alternateObjKey ? $alternateObjKey : $key );
        if ( !property_exists( $obj, $objKey ) ) {
            return;
        }
        $value = ( $obj->$objKey ? $obj->$objKey : $defaultValue );

        $this->setValue( $key, $value );
    }

    /**
     * Set a value
     *
     * @param        string
     * @param        string
     * @param        boolean
     *
     * @return void
     * @throws \ecsco\ormbase\exception\ObjectException
     */
    final public function setValue( string $key, $value, bool $ignoreMissing = false ): bool {

        try {
            parent::setValue( $key, $value, $ignoreMissing );
            return true;
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not find key '$key' in actual resultSet for '" . get_called_class() . "'"
            );
        }

        return false;
    }

    /**
     * Store an object
     *
     * @param boolean
     *
     * @return boolean|int
     * @throws \ecsco\ormbase\exception\ObjectException
     */
    public function store( bool $forceOverwritePrimaryKeys = false ) {

        try {
            $ret = parent::store( $forceOverwritePrimaryKeys );
            if ( is_numeric( $ret ) ) {
                $primKeys = $this->primaryKeys;
                $primKey = array_shift( $primKeys );
                $this->row[ $primKey ] = $ret;
            }
            $this->origRow = $this->row;
            $this->new = false;

            $this->clearCache();
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not store '" . preg_replace( '/^.*\\\\(.*)$/',
                                                                           '\1',
                                                                           get_called_class()
                                       ) . "' -> " . $ex->getMessage()
            );
        }

        return $ret;
    }

    /**
     * Clear cache
     * @return void
     */
    public function clearCache() {

        if ( $this instanceof CachableInterface ) {
            CacheAccess::getCacheInstance()->destroy( static::getCacheIdentifier() );
        }
    }

    /**
     * Get cache identifier string
     * @return string
     */
    public static function getCacheIdentifier(): string {

        return self::$cacheIdentifier;
    }

    /**
     * Delete an object
     * @return void
     * @throws \ecsco\ormbase\exception\ObjectException
     */
    public function delete(): bool {

        try {
            parent::delete();
            $this->clearCache();
            return true;
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not delete '" . get_called_class() . "' -> " . $ex->getMessage() );
        }

        return false;
    }

    /**
     * Get tableName
     * @return string
     */
    final protected function getTableName(): string {

        return static::TABLE_NAME;
    }

    /**
     * Revert an object
     * @return void
     */
    final public function revert() {

        $this->row = $this->origRow;
    }

    /**
     * To Array
     * @return string[]
     */
    public function toArray(): array {

        $rVal = [ ];
        $row = $this->getRow();
        if ( !empty( $this->staticRow ) ) {
            $row = array_merge( $row, $this->staticRow );
        }
        foreach ( $row AS $key => $value ) {
            if ( is_numeric( $value ) ) {
                $value = (float)$value;
            }
            if ( is_numeric( $key ) ) {
                $key = (float)$key;
            }

            if ( !is_array( $value ) ) {
                #
                # lets see if it is a date
                # if so we have to convert from
                # y-m-d h:i:s to y/m/d h:i:s (*sigh*)
                #
                if ( preg_match( '/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{2,4}.*?$/', $value ) ) {
                    try {
                        $date = new \DateTime( $value, new \DateTimeZone( ini_get( 'date.timezone' ) ) );
                        $value = $date->format( 'Y/m/d H:i:s' );
                    } catch ( \Exception $ex ) {
                        # nothing to do here
                    }
                }
            }

            $rVal[ $key ] = $value;
        }

        if ( $this->hiddenFields ) {
            $rVal = array_diff_key( $rVal, $this->hiddenFields );
        }

        return $rVal;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
