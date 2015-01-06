<?php
/**
 * Selection.php
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@kon.de>
 * @since 20150106 14:08
 */

namespace wplibs\database\mongo;

use wplibs\database\iSelection;
use wplibs\database\iSelectStrategy;

/**
 * Selection
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@kon.de>
 * @since 20150106 14:08
 */
class Selection implements iSelection {

	private $tables = [ ];
	private $where = [ ];
	private $sort = [ ];
	private $limit = [ ];
	private $set = [ ];
	private $createInfo = [ ];

	private $mode = ''; # can be select, delete, insert ...

	private $query = [ ];

	/**
	 * __construct
	 *
	 * @return Selection
	 */
	public function __construct() {
	}

	/**
	 * select
	 *
	 * @param iSelectStrategy $selector
	 * @return Selection
	 */
	public function select( iSelectStrategy $selector = null ) {
		if ( !$this->mode ) {
			$this->mode = 'find';
		}

		return $this;
	}

	/**
	 * create
	 *
	 * @param string $additionalInfo
	 * @return Selection
	 */
	public function create( $additionalInfo = '' ) {
		if ( !$this->mode ) {
			$this->mode = 'create';
		}
		$this->createInfo[ ] = $additionalInfo;

		return $this;
	}

	/**
	 * insert
	 *
	 * @return Selection
	 */
	public function insert() {
		if ( !$this->mode ) {
			$this->mode = 'insert';
		}

		return $this;
	}

	/**
	 * replace
	 *
	 * @return Selection
	 */
	public function replace() {
		if ( !$this->mode ) {
			$this->mode = 'findAndModify';
		}

		return $this;
	}

	/**
	 * delete
	 *
	 * @return Selection
	 */
	public function delete() {
		if ( !$this->mode ) {
			$this->mode = 'delete';
		}

		return $this;
	}

	/**
	 * from
	 *
	 * @param string $tableName
	 * @param string $alias
	 * @return Selection
	 */
	public function from( $tableName, $alias = '' ) {
		if ( $alias ) {
			$tableName = [ $tableName, $alias ];
		}
		$this->tables[ ] = $tableName;

		return $this;
	}

	/**
	 * into
	 *
	 * @param mixed $tableName
	 * @return Selection
	 */
	public function into( $tableName ) {
		$this->tables[ ] = $tableName;

		return $this;
	}

	/**
	 * table
	 *
	 * @param mixed  $tableName
	 * @param string $alias
	 * @param string $term
	 * @return Selection
	 */
	public function table( $tableName, $alias = '', $term = '' ) {
		if ( $alias || $term ) {
			$tableName = [ $tableName, $alias, $term ];
		}
		$this->tables[ ] = $tableName;

		return $this;
	}

	/**
	 * set
	 *
	 * @param mixed $fieldName
	 * @param mixed $fieldValue
	 * @return Selection
	 */
	public function set( $fieldName, $fieldValue ) {

		$this->set[ $fieldName ] = $fieldValue;

		return $this;
	}

	/**
	 * where
	 *
	 * @param mixed $fieldName
	 * @param mixed $operator
	 * @param mixed $fieldValue
	 * @internal param string $where
	 * @return Selection
	 */
	public function where( $fieldName, $operator, $fieldValue ) {

		$this->where[ ] = [ $fieldName, $operator, $fieldValue ];

		return $this;
	}

	/**
	 * sort
	 *
	 * @param mixed  $fieldName
	 * @param string $ascDesc
	 * @return Selection
	 */
	public function sort( $fieldName, $ascDesc = 'ASC' ) {
		$this->sort[ ] = " $fieldName " . strtoupper( $ascDesc ) . " ";

		return $this;
	}

	/**
	 * limit
	 *
	 * @param int $count
	 * @internal param mixed $fieldName
	 * @return Selection
	 */
	public function limit( $count = 1 ) {
		$this->limit = $count;

		return $this;
	}

	/**
	 * getQuery
	 *
	 * @return string
	 */
	public function getQuery() {
		$strName = "buildQuery" . ucfirst( $this->mode );

		$this->$strName();

		$return = [ $this->tables[ 0 ], $this->mode, $this->query ];

		return $return;
	}

	/**
	 * getQueryParams
	 *
	 * @return \string[]
	 * @throws \Exception
	 */
	public function getQueryParams() {
		throw new \Exception( 'invalid' );
	}

	/**
	 * duplicateKey
	 *
	 * @param mixed $fieldName
	 * @param mixed $fieldValue
	 * @throws \Exception
	 * @return \wplibs\database\iSelection|void
	 */
	public function duplicateKey( $fieldName, $fieldValue ) {
		throw new \Exception( 'invalid' );
	}

	/**
	 * view
	 *
	 * @param mixed $viewName
	 * @return \wplibs\database\iSelection|void
	 * @throws \Exception
	 */
	public function view( $viewName ) {
		throw new \Exception( 'invalid' );
	}

	/**
	 * unparameterize
	 *
	 * @return \wplibs\database\mongo\Selection
	 * @throws \Exception
	 */
	public function unparameterize() {
		throw new \Exception( 'invalid' );
	}

	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString() {
		if ( !$this->query ) {
			$this->getQuery();
		}

		return $this->query;
	}

	/**
	 * update
	 *
	 * @return Selection
	 */
	public function update() {
		if ( !$this->mode ) {
			$this->mode = 'update';
		}

		return $this;
	}

	/**
	 * buildQuery
	 *
	 * @return void
	 */
	protected function buildQueryFind() {

		if ( !$this->where || !$this->tables ) {
			return;
		}

		if ( $this->where ) {
			foreach ( $this->where AS list( $k, $o, $v ) ) {
				if ( $o == '=' ) {
					$this->query[ $k ] = $v;
				} else {
					$this->query[ $k ] = [ $o => $v ];
				}
			}
		}
	}

	/**
	 * buildQueryInsert
	 *
	 * @return void
	 */
	protected function buildQueryInsert() {

		if ( !$this->tables || !$this->set ) {
			return;
		}

		$this->query = $this->set;
	}

	/**
	 * buildQueryUpdate
	 *
	 * @return void
	 */
	protected function buildQueryUpdate() {

		if ( !$this->tables || !$this->set ) {
			return;
		}

	}

	/**
	 * buildQueryReplace
	 *
	 * @return void
	 */
	protected function buildQueryFindAndModify() {

		if ( !$this->tables ) {
			return;
		}

		$where = [ ];
		foreach ( $this->where AS list( $k, $o, $v ) ) {
			$where[ $k ] = $v;
		}
		$set = [ ];
		foreach ( $this->set AS $k => $v ) {
			$set[ $k ] = $v;
		}

		$this->query = [ 0 => $where, 1 => [ '$set' => $set ], 2 => null, 3 => [ 'upsert' => true ] ];
	}

	/**
	 * buildQueryDelete
	 *
	 * @return void
	 */
	protected function buildQueryDelete() {

		if ( !$this->tables ) {
			return;
		}

	}

	/**
	 * buildQueryView
	 *
	 * @return \string[]
	 * @throws \Exception
	 */
	protected function buildQueryView() {
		throw new \Exception( 'invalid' );
	}
}
