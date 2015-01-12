<?php
/**
 * class.aObject.php
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */

namespace wplibs\dbinterface;

use wplibs\cache\CacheAccess;
use wplibs\database\DBResultRow;
use wplibs\database\iDatabase;
use wplibs\exception\DatabaseException;
use wplibs\exception\ObjectException;
use wplibs\traits\tCall;
use wplibs\traits\tGet;

/**
 * class aObject
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */
abstract class aObject extends DBResultRow {

    use tCall;
    use tGet;

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
     * Construct
     *
     * @param array       $row
     * @param   iDatabase $db
     */
    public function __construct( array $row, iDatabase $db ) {

        parent::__construct( $row, $db );
    }

    /**
     * @param                            $row
     * @param                            $objectName
     * @param \wplibs\database\iDatabase $db
     *
     * @return aObject
     */
    public static function Factory( array $row, $objectName, iDatabase $db ) {

        if ( method_exists( $objectName, 'Factory' ) ) {
            return $objectName::Factory( $row, $objectName, $db );
        }

        return new $objectName();
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
*@param array $hiddenFields
     *
     * @return void
     */
    public function setHiddenFields( array $hiddenFields ) {

        $this->hiddenFields = $hiddenFields;
    }

    /**
     * Unhide a specific field
     *
     * @param string
     *
     * @return void
     */
    public function unhideField( $key ) {

        if ( $this->hasKey( $key ) && isset( $this->hiddenFields[ $key ] ) ) {
            unset( $this->hiddenFields[ $key ] );
        }
    }

    /**
     * Load full details
     *
     * @param boolean
     *
     * @return boolean
     */
    public function loadFullDetails( $forceReload = false ) {

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
     * @throws \wplibs\exception\ObjectException
     * @return mixed
     */
    final public function getValue( $key ) {

        try {
            $value = parent::getValue( $key );
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not find key '$key' in actual resultSet for '" . get_class( $this
                                       ) . "' " . var_export( $this->row, true )
            );
        }

        return $value;
    }

    /**
     * setValueByObject

     *
*@param mixed     $key
     * @param \stdClass $obj
     * @param null      $alternateObjKey
     * @param null      $defaultValue
     *
     * @throws \wplibs\exception\ObjectException
     * @return void
     */
    final public function setValueByObject( $key, \stdClass $obj, $alternateObjKey = null, $defaultValue = null ) {

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
*@param string
     * @param string
     * @param boolean
     *
     * @return void
     * @throws \wplibs\exception\ObjectException
     */
    final public function setValue( $key, $value, $ignoreMissing = false ) {

        try {
            parent::setValue( $key, $value, $ignoreMissing );
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not find key '$key' in actual resultSet for '" . get_class( $this ) . "'"
            );
        }
    }

    /**
     * Store an object
     *
     * @param boolean
     *
     * @return boolean|int
     * @throws \wplibs\exception\ObjectException
     */
    public function store( $forceOverwritePrimaryKeys = false ) {

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
                                                                           get_class( $this )
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

        if ( $this instanceof iCachable ) {
            CacheAccess::getCacheInstance()->destroy( static::getCacheIdentifier() );
        }
    }

    /**
     * Get cache identifier string
     * @return string
     */
    public static function getCacheIdentifier() {

        return self::$cacheIdentifier;
    }

    /**
     * Delete an object
     * @return void
     * @throws \wplibs\exception\ObjectException
     */
    public function delete() {

        try {
            parent::delete();
            $this->clearCache();
        } catch ( DatabaseException $ex ) {
            throw new ObjectException( "Could not delete '" . get_class( $this ) . "' -> " . $ex->getMessage() );
        }
    }

    /**
     * Get tableName
     * @return string
     */
    final protected function getTableName() {

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
    public function toArray() {

        $rVal = [ ];
        $row = $this->getRow();
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
                if ( stristr( $value, '-' ) ) {
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