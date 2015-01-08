<?php
/**
 * Contact.php
 *
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 */

namespace wplibs\dbinterface;

    /*
    use wplibs\database\iDatabase;
    */
/**
 * class Contact
 *
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:09
 * class Contact extends aObject implements iCachable {
 * const TABLE_NAME     = 'contact';
 * const CACHE_TYPE     = 'contact';
 * const LOG_TABLE_NAME = '';
 * protected $writeLog    = false;
 * protected $primaryKeys = [ 'id' ];
 * protected $hiddenFields = [ 'F_contact_media_id'        => null,
 * 'F_contact_geo_location_id' => null,
 * 'contact_created_by'        => null,
 * 'last_used_login_method'    => null,
 * 'contact_password'          => null,
 * 'contact_activation_code'   => null,
 * 'contact_imei'              => null
 * ];
 * protected $loaded       = false;
 * protected $contactList  = [ ];
 * protected $icon        = null;
 * protected $geoLocation = null;
 * public static function Factory( array $row, iDatabase $db ) {
 * if ( $row === null ) {
 * return null;
 * }
 * $objName = '\wplibs\dbinterface\Contact';
 * #
 * # Hint: A Group cannot be a developer (yet)
 * #
 * if ( isset( $row[ 'contact_type' ] ) && $row[ 'contact_type' ] == 'group' ) {
 * $objName = '\wplibs\dbinterface\ContactGroup';
 * }
 * elseif ( isset( $row[ 'contact_developer' ] ) && $row[ 'contact_developer' ] == 'yes' ) {
 * $objName = '\wplibs\dbinterface\DevContact';
 * }
 * $obj = new $objName( $row, $db );
 * return $obj;
 * }
 * public function addContactToContactList( Contact $c, $localAlias = '', $memberStatus = 'accepted' ) {
 * $cmc = ContactMemberContainer::getNamedInstance( $this->getConfigName() );
 * $cmc->addContactAsMember( $this, $c, $localAlias, $memberStatus );
 * }
 * public function deleteContactFromContactList( Contact $c ) {
 * $cmc = ContactMemberContainer::getNamedInstance( $this->getConfigName() );
 * $cmc->deleteContactAsMember( $this, $c );
 * }
 * public function hasContactInContactList( Contact $c ) {
 * $cc = $this->getContactContactList();
 * foreach ( $cc AS $ccc ) {
 * if ( $ccc->getValue( 'id' ) == $c->getValue( 'id' ) ) {
 * return true;
 * }
 * }
 * return false;
 * }
 * public function getContactContactList() {
 * $clc = ContactListContainer::getNamedInstance( $this->getConfigName() );
 * $contactList = $clc->getContactListByContact( $this );
 * return $contactList;
 * }
 * public function getContactContactListContactsByIDs( array $ids ) {
 * $clc = ContactListContainer::getNamedInstance( $this->getConfigName() );
 * $contacts = $clc->getContactListContactsByContactAndIDs( $this, $ids );
 * return $contacts;
 * }
 * public function getContactConversationByTarget( $targetContactID ) {
 * $conversations = $this->getContactConversations();
 * foreach ( $conversations AS $conv ) {
 * if ( $conv->getValue( 'F_to_contact_id' ) == $targetContactID ) {
 * return $conv;
 * }
 * }
 * return null;
 * }
 * public function getContactConversations() {
 * $cc = ConversationContainer::getNamedInstance( $this->getConfigName() );
 * $conversations = $cc->getConversationsByContact( $this );
 * return $conversations;
 * }
 * public function getContactConversationsByIDs( array $ids ) {
 * $cc = ConversationContainer::getNamedInstance( $this->getConfigName() );
 * $conversations = $cc->getConversationsByContactAndIDs( $this, $ids );
 * return $conversations;
 * }
 * public function getContactMessagesByIDs( array $ids ) {
 * $mc = MessageContainer::getNamedInstance( $this->getConfigName() );
 * $messages = $mc->getMessagesByIDs( $ids );
 * return $messages;
 * }
 * public function getContactConversationBySourceOrTarget( $targetContactID ) {
 * $conversations = $this->getContactConversations();
 * foreach ( $conversations AS $conv ) {
 * if ( $conv->getValue( 'F_from_contact_id' ) == $targetContactID ||
 * $conv->getValue( 'F_to_contact_id' ) == $targetContactID
 * ) {
 * return $conv;
 * }
 * }
 * return null;
 * }
 * public function getContactMedias() {
 * $mc = MediaContainer::getNamedInstance( $this->getConfigName() );
 * $medias = $mc->getMediasByContact( $this );
 * return $medias;
 * }
 * public function addContactLatestChange( $objType, $objID ) {
 * $ids = [ $objID ];
 * #
 * # append if found
 * #
 * $currentChanges = $this->getContactLatestChanges();
 * foreach ( $currentChanges AS $currentChange ) {
 * if ( $currentChange->getValue( 'change_type' ) == $objType ) {
 * $ids = $currentChange->getValue( 'changed_ids' );
 * $ids[ ] = $objID;
 * break;
 * }
 * }
 * if ( empty( $ids[ 0 ] ) ) {
 * $ids = [ ];
 * }
 * else {
 * $ids = array_keys( array_flip( $ids ) );
 * }
 * $lcc = ContactChangeContainer::getNamedInstance( $this->getConfigName() );
 * $change = $lcc->createNew();
 * $change->setValue( 'id', $this->getValue( 'id' ) );
 * $change->setValue( 'change_type', $objType );
 * $change->setValue( 'changed_ids', $ids );
 * $change->store();
 * }
 * public function getContactLatestChanges() {
 * $lcc = ContactChangeContainer::getNamedInstance( $this->getConfigName() );
 * $lc = $lcc->getContactChangesByContact( $this );
 * return $lc;
 * }
 * public function clearLatestChanges() {
 * $changes = $this->getContactLatestChanges();
 * foreach ( $changes AS $change ) {
 * $change->delete();
 * }
 * }
 * public function getIcon() {
 * $this->loadFullDetails();
 * return $this->icon;
 * }
 * public function loadFullDetails( $forceReload = false ) {
 * if ( ( $this->loaded && !$forceReload ) || $this->new ) {
 * return true;
 * }
 * if ( $this->getValue( 'F_contact_media_id' ) ) {
 * $mc = MediaContainer::getNamedInstance( $this->getConfigName() );
 * $this->icon = $mc->getMediaByID( $this->getValue( 'F_contact_media_id' ) );
 * }
 * if ( $this->getValue( 'F_contact_geo_location_id' ) ) {
 * $gc = GeoLocationContainer::getNamedInstance( $this->getConfigName() );
 * $this->geoLocation = $gc->getGeoLocationByID( $this->getValue( 'F_contact_geo_location_id' ) );
 * }
 * return parent::loadFullDetails();
 * }
 * public function toArray() {
 * $rVal = parent::toArray();
 * $this->loadFullDetails();
 * if ( $this->icon !== null ) {
 * $rVal[ 'icon' ] = $this->icon->toArray();
 * }
 * if ( $this->geoLocation !== null ) {
 * $rVal[ 'geo_location' ] = $this->geoLocation->toArray();
 * }
 * $return = [ 'type' => $this->getShortClassName(), 'object' => $rVal ];
 * return $return;
 * }
 * public function store( $forceOverwritePrimaryKeys = false ) {
 * if ( $this->new ) {
 * $this->setValue( 'contact_created', ( new \DateTime() )->format( 'Y-m-d H:i:s' ) );
 * }
 * if ( !$this->getValue( 'contact_phone' ) ) {
 * $this->setValue( 'contact_phone', null );
 * }
 * $rVal = parent::store( $forceOverwritePrimaryKeys );
 * return $rVal;
 * }
 * public function getGeoLocation() {
 * $this->loadFullDetails();
 * return $this->geoLocation;
 * }
 * }
 */