<?php
/**
 * class.DBResultRow.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace ecsco\ormbase\database;

use ecsco\ormbase\charset\CharsetConversion;
use ecsco\ormbase\config\Config;
use ecsco\ormbase\exception\DatabaseException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * class DBResultRow
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
class DBResultRow {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;

    const TABLE_NAME = '';

    /**
     * Is this row new?
     * @var bool
     */
    protected $new = true;
    /**
     * The row itself
     * @var array
     */
    protected $row = [ ];
    /**
     * Original row to state changes
     * @var array
     */
    protected $origRow = [ ];
    /**
     * Primary key(s) of this row
     * @var array
     */
    protected $primaryKeys = [ ];

    /**
     * List of what has been changed
     * @var array
     */
    private $changed = [ ];
    /**
     * Already deleted?
     * @var bool
     */
    private $deleted = false;

    /**
     * Create new DBResultRow
     *
     * @param array             $row
     * @param DatabaseInterface $database
     */
    protected function __construct( array $row, DatabaseInterface $database ) {

        $this->origRow = $row;
        $this->row = $row;
        $this->databaseConfig = $database->getConfigName();
    }

    /**
     * Get (db) row
     * @return array
     */
    final public function getRow() {

        return $this->row;
    }

    /**
     * Get primary keys
     * @return array
     */
    final public function getPrimaryKeys() {

        return $this->primaryKeys;
    }

    /**
     * Set new
     *
     * @param $new
     *
     * @return void
     */
    final public function isNew( $new ) {

        $this->new = $new;
    }

    /**
     * Get a value
     *
     * @param $key
     *
     * @return string
     * @throws \ecsco\ormbase\exception\CharsetConversionException
     * @throws \ecsco\ormbase\exception\DatabaseException
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
     * @param $key
     *
     * @return bool
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
     * @throws \ecsco\ormbase\exception\CharsetConversionException
     * @throws \ecsco\ormbase\exception\DatabaseException
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
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return boolean|int
     */
    protected function store( $forceOverwritePrimaryKeys = false ) {

        if ( $this->deleted ) {
            throw new DatabaseException( "Could not store deleted object -> " . var_export( $this->row, true ) );
        }

        if ( !$this->changed && !$this->new ) {
            return true;
        }

        $db = DatabaseAccess::getDatabaseInstance( Config::getInstance() );

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
     * Get the table name
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

        $db = DatabaseAccess::getDatabaseInstance( Config::getInstance() );

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
