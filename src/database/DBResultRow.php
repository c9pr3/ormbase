<?php
/**
 * class.DBResultRow.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace wplibs\database;

use wplibs\charset\CharsetConversion;
use wplibs\config\Config;
use wplibs\exception\DatabaseException;

/**
 * class DBResultRow
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
class DBResultRow {

    const TABLE_NAME = '';

    /**
     * @var bool
     */
    protected $new = true;
    /**
     * @var array
     */
    protected $row = [ ];
    /**
     * @var array
     */
    protected $origRow = [ ];
    /**
     * @var array
     */
    protected $primaryKeys = [ ];

    /**
     * @var array
     */
    private $changed = [ ];
    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * Create new DBResultRow
     *
     * @param array     $row
     * @param iDatabase $database
     */
    protected function __construct( array $row, iDatabase $database ) {

        $this->origRow = $row;
        $this->row = $row;
        $this->databaseConfig = $database->getConfigName();
    }

    /**
     * Get (db) row
     * @return string[]
     */
    final public function getRow() {

        return $this->row;
    }

    /**
     * Get primary keys
     * @return string[]
     */
    final public function getPrimaryKeys() {

        return $this->primaryKeys;
    }

    /**
     * Set new
     *
     * @param boolean
     *
     * @return void
     */
    final public function isNew( $new ) {

        $this->new = $new;
    }

    /**
     * Get a value
     *
     * @param string
     *
     * @throws \wplibs\exception\CharsetConversionException
     * @throws \wplibs\exception\DatabaseException
     * @return string
     */
    protected function getValue( $key ) {

        if ( !$this->hasKey( $key ) ) {
            throw new DatabaseException( "Could not find key '$key' in actual result set" );
        }

        if ( is_array( $this->row[ $key ] ) ) {
            return $this->row[ $key ];
        }
        else {
            return CharsetConversion::toUTF8( $this->row[ $key ] );
        }
    }

    /**
     * Check if key exists
     *
     * @param mixed
     *
     * @return boolean
     */
    protected function hasKey( $key ) {

        return ( @array_key_exists( $key, $this->row ) );
    }

    /**
     * Set a value
     *
     * @param      $key
     * @param      $value
     * @param bool $ignoreMissing
     *
     * @return bool
     * @throws \wplibs\exception\CharsetConversionException
     * @throws \wplibs\exception\DatabaseException
     */
    protected function setValue( $key, $value, $ignoreMissing = false ) {

        if ( $value instanceof \DateTime ) {
            $value = $value->format( 'Y-m-d H:i:s' );
        }

        $key = CharsetConversion::toUTF8( $key );
        if ( !is_array( $value ) && !is_object( $value ) && $value !== null ) {
            $value = CharsetConversion::toUTF8( $value );
        }

        if ( !array_key_exists( $key, $this->row ) ) {
            if ( !$ignoreMissing ) {
                throw new DatabaseException( "Could not find key '$key' in actual result set" );
            }
        }
        else {
            if ( $value === $this->row[ $key ] ) {
                return true;
            }
        }

        $this->row[ $key ] = $value;

        if ( !in_array( $key, $this->primaryKeys ) ) {
            $this->changed[ $key ] = $value;
        }

        return true;
    }

    /**
     * Store this row back to database
     *
     * @param bool $forceOverwritePrimaryKeys
     *
     * @throws \wplibs\exception\DatabaseException
     * @return boolean|int
     */
    protected function store( $forceOverwritePrimaryKeys = false ) {

        if ( $this->deleted ) {
            throw new DatabaseException( "Could not store deleted object -> " . var_export( $this->row, true ) );
        }

        if ( !$this->changed && !$this->new ) {
            return true;
        }

        $db = Config::getInstance()->getDatabase();

        if ( $this->new === true ) {
            $sql = $db->insert()->into( $this->getTableName() );
            foreach ( $this->row AS $k => $v ) {
                if ( $v !== null ) {
                    $sql->set( $k, $v );
                }
            }
            $params = $sql->getQueryParams();

            $db->prepareQuery( $sql, ...$params );
            /** @noinspection PhpUndefinedFieldInspection */
            $res = $db->insert_id;
            $this->new = false;

            return $res;
        }

        $sql = $db->update()->table( $this->getTableName() );
        foreach ( $this->primaryKeys AS $k ) {
            $sql->where( $k, '=', $this->row[ $k ] );
        }
        foreach ( $this->row AS $k => $v ) {
            if ( ( in_array( $k, $this->primaryKeys ) && !$forceOverwritePrimaryKeys ) ) {
                continue;
            }

            $sql->set( $k, $v );
        }

        $params = $sql->getQueryParams();

        $res = $db->prepareQuery( $sql, ...$params );

        return $res;
    }

    /**
     * @return mixed
     */
    protected function getTableName() {

        return static::TABLE_NAME;
    }

    /**
     * Delete a row from database
     * @return boolean
     */
    protected function delete() {

        if ( $this->new ) {
            $this->deleted = true;

            return true;
        }

        if ( $this->deleted ) {
            return true;
        }

        $db = Config::getInstance()->getDatabase();
        $sql = $db->delete()->from( $this->getTableName() );
        foreach ( $this->primaryKeys AS $k ) {
            $sql->where( $k, '=', $this->row[ $k ] );
        }
        $params = $sql->getQueryParams();

        $res = $db->prepareQuery( $sql, ...$params );

        $this->deleted = true;

        return $res;
    }

    /**
     * Get database
     * @return iDatabase
     */
    protected function getDatabase() {

        return Config::getInstance()->getDatabase();
    }

    /**
     * Get config name
     * @return string
     */
    protected function getConfigName() {

        return $this->databaseConfig;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
