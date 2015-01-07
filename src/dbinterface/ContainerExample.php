<?php
/**
 * class.ContactContainer.php
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */

namespace wplibs\dbinterface;

    /*
    use wplibs\database\FieldSelection;
    use wplibs\traits\tGetNamedInstance;
    */

    /**
     * class ContactContainer
     * @package    WPLIBS
     * @subpackage DBINTERFACE
     * @author     Christian Senkowski <cs@e-cs.co>
     * @since      20150106 14:09
     * class ContactContainer extends aContainer {
     * const OBJECT_NAME = 'Contact';
     * const TABLE_NAME  = Contact::TABLE_NAME;
     * private static $instances   = null;
     * protected      $basicFields = [ 'id', 'contact_active', 'F_contact_media_id' ];
     * private        $configName  = '';
     * use tGetNamedInstance;
     * protected function __construct( $name ) {
     * parent::__construct( $name );
     * self::$instances[ $name ] = $this;
     * $this->configName = $name;
     * }
     * public function createNewContact( $alias, $password, $gender, $phone, $imei ) {
     * $obj = parent::createNew();
     * if ( !$gender ) {
     * $gender = 'unknown';
     * }
     * $obj->setValue( 'contact_alias', $alias );
     * $obj->setValue( 'contact_password', $password );
     * $obj->setValue( 'contact_gender', $gender );
     * $obj->setValue( 'contact_type', 'contact' );
     * $obj->setValue( 'contact_active', 'no' );
     * $obj->setValue( 'contact_phone', $phone );
     * $obj->setValue( 'contact_imei', $imei );
     * $obj->store();
     * #
     * # create view for contact_media
     * #
     * $sql =
     * $this->getDatabase()->create( 'ALGORITHM=MERGE' )->view( 'v_contact_media_' .
     * $obj->getValue( 'id' ) )->select( new FieldSelection() )->from( 'media' )->where( 'F_contact_id', '=',
     * $obj->getValue( 'id' ) );
     * $this->getDatabase()->query( $sql->getQuery()->unparameterize() );
     * #
     * # create view for contact list
     * #
     * $sql =
     * $this->getDatabase()->create( 'ALGORITHM=MERGE' )->view( 'v_contact_list_' .
     * $obj->getValue( 'id' ) )->select( new FieldSelection( 'c.*', 'cm.member_local_alias',
     * 'cm.contact_member_status', 'm.media_type', 'm.media_visibility', 'm.media_name', 'm.media_path', 'g.geo_time',
     * 'g.geo_accuracy', 'X(g.geo_point) AS geo_latitude', 'Y(g.geo_point) AS geo_longitude' ) )->from(
     * 'contact_has_contacts', 'cm' )->table( 'contact', 'c', 'ON c.id=cm.F_contact_member_id' )->table( 'media', 'm',
     * 'ON m.id=c.F_contact_media_id' )->table( 'geo_location', 'g', 'ON g.id=c.F_contact_geo_location_id' )->where(
     * 'cm.F_contact_owner_id', '=', $obj->getValue( 'id' ) );
     * $this->getDatabase()->query( $sql->getQuery()->unparameterize() );
     * #
     * # create view for contact conversations
     * #
     * $sql =
     * $this->getDatabase()->create( 'ALGORITHM=MERGE' )->view( 'v_contact_conversation_' .
     * $obj->getValue( 'id' ) )->select( new FieldSelection( 'cv.*', 'cvl.conversation_state', 'm.media_type',
     * 'm.media_visibility', 'm.media_name', 'm.media_path' ) )->from( 'conversation', 'cv' )->table(
     * 'conversation_listener', 'cvl', 'ON cvl.F_conversation_id=cv.id' )->table( 'media', 'm', 'ON
     * m.id=cv.F_conversation_media_id' )->where( 'cvl.F_contact_id', '=', $obj->getValue( 'id' ) );
     * $this->getDatabase()->query( $sql->getQuery()->unparameterize() );
     * #
     * # create activation code
     * #
     * $activationCode =
     * password_hash( $obj->getValue( 'contact_alias' ) . $obj->getValue( 'contact_password' ), PASSWORD_BCRYPT );
     * $obj->setValue( 'contact_activation_code', $activationCode );
     * $obj->store();
     * $obj->addContactLatestChange( 'contact', null );
     * $obj->addContactLatestChange( 'conversation', null );
     * $obj->addContactLatestChange( 'message', null );
     * return $obj;
     * }
     * public function getContactByID( $id, FieldSelection $selector = null ) {
     * $sql =
     * $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'id', '=', (int)$id )->limit( 1 );
     * $params = $sql->getQueryParams();
     * $result = $this->makePreparedObject( $sql, self::OBJECT_NAME, ...$params );
     * return $result;
     * }
     * public function getContactsByIMEI( $imei, FieldSelection $selector = null ) {
     * $sql =
     * $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'contact_imei', '=', (string)$imei
     * );
     * $params = $sql->getQueryParams();
     * $result = $this->makePreparedObjects( $sql, self::OBJECT_NAME, ...$params );
     * return $result;
     * }
     * public function getContactByPhone( $phone, FieldSelection $selector = null ) {
     * $sql =
     * $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'contact_phone', '=', $phone
     * )->limit( 1 );
     * $params = $sql->getQueryParams();
     * $result = $this->makePreparedObject( $sql, self::OBJECT_NAME, ...$params );
     * return $result;
     * }
     * public function getContactsByGeoLocations( array $geoLocations, FieldSelection $selector = null ) {
     * $geoLocationIDs = [ ];
     * foreach ( $geoLocations AS $g ) {
     * $geoLocationIDs[ ] = $g->getValue( 'id' );
     * }
     * $sql =
     * $this->getDatabase()->select( $selector )->from( self::TABLE_NAME )->where( 'F_contact_geo_location_id', 'IN',
     * '(' .
     * implode( ',', $geoLocationIDs ) .
     * ')' );
     * $params = $sql->getQueryParams();
     * $result = $this->makePreparedObjects( $sql, self::OBJECT_NAME, ...$params );
     * return $result;
     * }
     * }
     */
