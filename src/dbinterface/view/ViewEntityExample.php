<?php
/**
 * class.Contact.php
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:10
 */

namespace wplibs\dbinterface\view;

use wplibs\database\iDatabase;
use wplibs\dbinterface\Contact;
use wplibs\dbinterface\GeoLocation;
use wplibs\dbinterface\iCachable;
use wplibs\dbinterface\Media;

/**
 * class Contact
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:10
 */
final class ViewContact extends Contact implements iCachable {

	const CACHE_TYPE = 'contact';

	protected $writeLog = false;
	protected $primaryKeys = [ 'id' ];
	protected $hiddenFields = [ 'F_contact_media_id' => null, 'F_contact_geo_location_id' => null, 'contact_created_by' => null, 'last_used_login_method' => null, 'contact_password' => null, 'contact_activation_code' => null, 'member_local_alias' => null, 'contact_imei' => null ];

	protected $loaded = false;

	/**
	 * __construct
	 *
	 * @param array     $row
	 * @param iDatabase $db
	 * @return ViewContact
	 */
	public function __construct( array $row, iDatabase $db ) {
		$this->performMapping( $row );
		parent::__construct( $row, $db );
	}

	/**
	 * Perform internal mappings
	 *
	 * @param
	 * @return void
	 */
	public function performMapping( &$row ) {

		if ( !$row ) {
			return;
		}

		#
		# icon
		#
		$row[ 'icon' ] = [ ];

		$row[ 'icon' ][ 'id' ] = $row[ 'F_contact_media_id' ];
		unset( $row[ 'F_contact_media_id' ] );

		$row[ 'icon' ][ 'media_type' ] = $row[ 'media_type' ];
		unset( $row[ 'media_type' ] );

		$row[ 'icon' ][ 'media_visibility' ] = $row[ 'media_visibility' ];
		unset( $row[ 'media_visibility' ] );

		$row[ 'icon' ][ 'media_name' ] = $row[ 'media_name' ];
		unset( $row[ 'media_name' ] );

		$row[ 'icon' ][ 'media_path' ] = $row[ 'media_path' ];
		unset( $row[ 'media_path' ] );

		if ( $row[ 'icon' ] && $row[ 'icon' ][ 'id' ] ) {
			$row[ 'icon' ] = Media::Factory( $row[ 'icon' ], $this->getDatabase() );
		}

		#
		# geo location
		#
		$row[ 'geo_location' ] = [ ];

		$row[ 'geo_location' ][ 'id' ] = $row[ 'F_contact_geo_location_id' ];
		unset( $row[ 'F_contact_geo_location_id' ] );

		$row[ 'geo_location' ][ 'geo_time' ] = $row[ 'geo_time' ];
		unset( $row[ 'geo_time' ] );

		$row[ 'geo_location' ][ 'geo_accuracy' ] = $row[ 'geo_accuracy' ];
		unset( $row[ 'geo_accuracy' ] );

		$row[ 'geo_location' ][ 'geo_longitude' ] = $row[ 'geo_longitude' ];
		unset( $row[ 'geo_longitude' ] );

		$row[ 'geo_location' ][ 'geo_latitude' ] = $row[ 'geo_latitude' ];
		unset( $row[ 'geo_latitude' ] );

		if ( $row[ 'geo_location' ] && $row[ 'geo_location' ][ 'id' ] ) {
			$row[ 'geo_location' ] = GeoLocation::Factory( $row[ 'geo_location' ], $this->getDatabase() );
		}

		#
		# local alias
		#
		if ( $row[ 'member_local_alias' ] != '' ) {
			$row[ 'contact_alias' ] = $row[ 'member_local_alias' ];
			unset( $row[ 'member_local_alias' ] );
		}
	}

	/**
	 * Factory
	 *
	 * @param array                     $row
	 * @param \wplibs\database\iDatabase $db
	 * @internal param $DBResultRow
	 * @internal param $Database
	 * @return Contact
	 */
	public static function Factory( array $row, iDatabase $db ) {
		if ( $row === null ) {
			return null;
		}

		$objName = '\wplibs\dbinterface\view\ViewContact';
		if ( isset( $row[ 'contact_type' ] ) && $row[ 'contact_type' ] == 'group' ) {
			$objName = '\wplibs\dbinterface\view\ViewContactGroup';
		}

		$obj = new $objName( $row, $db );

		return $obj;
	}

	/**
	 * @return array
	 */
	public function toArray() {

		$rVal = '';

		$row = $this->getRow();
		if ( isset( $row[ 'icon' ] ) ) {
			$icon = $row[ 'icon' ];
			unset( $row[ 'icon' ] );
		}
		if ( isset( $row[ 'geo_location' ] ) ) {
			$geoLocation = $row[ 'geo_location' ];
			unset( $row[ 'geo_location' ] );
		}

		foreach ( $row AS $key => $value ) {
			if ( is_numeric( $value ) ) {
				$value = (float)$value;
			}
			if ( is_numeric( $key ) ) {
				$key = (float)$key;
			}

			#
			# lets see if it is a date
			# if so we have to convert from
			# y-m-d h:i:s to y/m/d h:i:s (*sigh*)
			#
			if ( stristr( $value, '-' ) ) {
				try {
					$date = new \DateTime( $value, new \DateTimeZone( ini_get( 'date.timezone' ) ) );
					$value = $date->format( 'Y/m/d H:i:s' );
				}
				catch ( \Exception $ex ) {
					# nothing to do here
				}
			}

			$rVal[ $key ] = $value;
		}

		if ( isset( $icon ) && $icon instanceof Media ) {
			/** @noinspection PhpUndefinedMethodInspection */
			$rVal[ 'icon' ] = $icon->toArray();
		}

		if ( isset( $geoLocation ) && $geoLocation instanceof GeoLocation ) {
			/** @noinspection PhpUndefinedMethodInspection */
			$rVal[ 'geo_location' ] = $geoLocation->toArray();
		}

		if ( $this->hiddenFields ) {
			$rVal = array_diff_key( $rVal, $this->hiddenFields );
		}

		$return = [ 'type' => $this->getShortClassName(), 'object' => $rVal ];

		return $return;
	}
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
