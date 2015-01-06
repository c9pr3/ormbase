<?php
/**
 * Contact.php
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:09
 */

namespace wplibs\dbinterface;

use wplibs\database\iDatabase;

/**
 * class Contact
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:09
 */
class Contact extends aObject implements iCachable {

	const TABLE_NAME = 'contact';
	const CACHE_TYPE = 'contact';
	const LOG_TABLE_NAME = '';

	protected $writeLog = false;
	protected $primaryKeys = [ 'id' ];

	protected $hiddenFields = [ 'F_contact_media_id' => null, 'F_contact_geo_location_id' => null, 'contact_created_by' => null, 'last_used_login_method' => null, 'contact_password' => null, 'contact_activation_code' => null, 'contact_imei' => null ];
	protected $loaded = false;
	protected $contactList = [ ];

	protected $icon = null;
	protected $geoLocation = null;

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

		$objName = '\wplibs\dbinterface\Contact';

		#
		# Hint: A Group cannot be a developer (yet)
		#
		if ( isset( $row[ 'contact_type' ] ) && $row[ 'contact_type' ] == 'group' ) {
			$objName = '\wplibs\dbinterface\ContactGroup';
		} elseif ( isset( $row[ 'contact_developer' ] ) && $row[ 'contact_developer' ] == 'yes' ) {
			$objName = '\wplibs\dbinterface\DevContact';
		}

		$obj = new $objName( $row, $db );

		return $obj;
	}

	/**
	 * addContactToContactList
	 *
	 * @param \wplibs\dbinterface\Contact $c
	 * @param null|string                $localAlias
	 * @param string                     $memberStatus
	 * @return void
	 */
	public function addContactToContactList( Contact $c, $localAlias = '', $memberStatus = 'accepted' ) {

		$cmc = ContactMemberContainer::getNamedInstance( $this->getConfigName() );
		$cmc->addContactAsMember( $this, $c, $localAlias, $memberStatus );

	}

	/**
	 * deleteContactFromContactList
	 *
	 * @param Contact $c
	 * @return void
	 */
	public function deleteContactFromContactList( Contact $c ) {

		$cmc = ContactMemberContainer::getNamedInstance( $this->getConfigName() );
		$cmc->deleteContactAsMember( $this, $c );

	}

	/**
	 * hasContactInContactList
	 *
	 * @param Contact $c
	 * @return boolean
	 */
	public function hasContactInContactList( Contact $c ) {
		$cc = $this->getContactContactList();
		foreach ( $cc AS $ccc ) {
			if ( $ccc->getValue( 'id' ) == $c->getValue( 'id' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get contact list
	 *
	 * @return Contact[]
	 */
	public function getContactContactList() {

		$clc = ContactListContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$contactList = $clc->getContactListByContact( $this );

		return $contactList;
	}

	/**
	 * Get contact list contacts by ids
	 *
	 * @param array $ids
	 * @return Contact[]
	 */
	public function getContactContactListContactsByIDs( array $ids ) {

		$clc = ContactListContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$contacts = $clc->getContactListContactsByContactAndIDs( $this, $ids );

		return $contacts;
	}

	/**
	 * Get contact conversation by target id
	 *
	 * @param $targetContactID
	 * @throws \ObjectException
	 * @return Conversation
	 */
	public function getContactConversationByTarget( $targetContactID ) {

		$conversations = $this->getContactConversations();
		foreach ( $conversations AS $conv ) {
			if ( $conv->getValue( 'F_to_contact_id' ) == $targetContactID ) {
				return $conv;
			}
		}

		return null;
	}

	/**
	 * Get contact conversations
	 *
	 * @return Conversation[]
	 */
	public function getContactConversations() {

		$cc = ConversationContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$conversations = $cc->getConversationsByContact( $this );

		return $conversations;
	}

	/**
	 * Get contact conversations by ids
	 *
	 * @param array $ids
	 * @return Conversation[]
	 */
	public function getContactConversationsByIDs( array $ids ) {

		$cc = ConversationContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$conversations = $cc->getConversationsByContactAndIDs( $this, $ids );

		return $conversations;
	}

	/**
	 * Get contact messages by ids
	 *
	 * @param array $ids
	 * @return MEssage[]
	 */
	public function getContactMessagesByIDs( array $ids ) {

		$mc = MessageContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$messages = $mc->getMessagesByIDs( $ids );

		return $messages;
	}

	/**
	 * Get contact conversation by source or target id
	 *
	 * @param $targetContactID
	 * @throws \ObjectException
	 * @return Conversation
	 */
	public function getContactConversationBySourceOrTarget( $targetContactID ) {
		$conversations = $this->getContactConversations();
		foreach ( $conversations AS $conv ) {
			if ( $conv->getValue( 'F_from_contact_id' ) == $targetContactID || $conv->getValue( 'F_to_contact_id' ) == $targetContactID ) {
				return $conv;
			}
		}

		return null;
	}

	/**
	 * Get contact medias
	 *
	 * @return Media[]
	 */
	public function getContactMedias() {

		$mc = MediaContainer::getNamedInstance( $this->getConfigName() );
		/** @noinspection PhpUndefinedMethodInspection */
		$medias = $mc->getMediasByContact( $this );

		return $medias;
	}

	/**
	 * addContactLatestChange
	 *
	 * object_relation can be
	 * contact
	 * contact_list
	 * conversation
	 * message
	 * media
	 *
	 * @param $objType
	 * @param $objID
	 * @throws \ObjectException
	 * @return void
	 */
	public function addContactLatestChange( $objType, $objID ) {

		$ids = [ $objID ];

		#
		# append if found
		#
		$currentChanges = $this->getContactLatestChanges();
		foreach ( $currentChanges AS $currentChange ) {
			if ( $currentChange->getValue( 'change_type' ) == $objType ) {
				$ids = $currentChange->getValue( 'changed_ids' );
				$ids[ ] = $objID;
				break;
			}
		}
		if ( empty( $ids[ 0 ] ) ) {
			$ids = [ ];
		} else {
			$ids = array_keys( array_flip( $ids ) );
		}
		$lcc = ContactChangeContainer::getNamedInstance( $this->getConfigName() );
		$change = $lcc->createNew();
		$change->setValue( 'id', $this->getValue( 'id' ) );
		$change->setValue( 'change_type', $objType );
		$change->setValue( 'changed_ids', $ids );
		$change->store();
	}

	/**
	 * getContactLatestChanges
	 *
	 * @return \wplibs\dbinterface\ContactChange[]
	 */
	public function getContactLatestChanges() {
		$lcc = ContactChangeContainer::getNamedInstance( $this->getConfigName() );
		$lc = $lcc->getContactChangesByContact( $this );

		return $lc;
	}

	/**
	 * clearLatestChanges
	 *
	 * @return void
	 */
	public function clearLatestChanges() {
		$changes = $this->getContactLatestChanges();
		foreach ( $changes AS $change ) {
			$change->delete();
		}
	}

	/**
	 * getIcon
	 *
	 * @return \wplibs\dbinterface\Media|null
	 */
	public function getIcon() {
		$this->loadFullDetails();

		return $this->icon;
	}

	/**
	 * Overwritten load full details
	 *
	 * @param boolean
	 * @return boolean
	 */
	public function loadFullDetails( $forceReload = false ) {
		if ( ( $this->loaded && !$forceReload ) || $this->new ) {
			return true;
		}

		if ( $this->getValue( 'F_contact_media_id' ) ) {
			$mc = MediaContainer::getNamedInstance( $this->getConfigName() );
			/** @noinspection PhpUndefinedMethodInspection */
			$this->icon = $mc->getMediaByID( $this->getValue( 'F_contact_media_id' ) );
		}
		if ( $this->getValue( 'F_contact_geo_location_id' ) ) {
			$gc = GeoLocationContainer::getNamedInstance( $this->getConfigName() );
			/** @noinspection PhpUndefinedMethodInspection */
			$this->geoLocation = $gc->getGeoLocationByID( $this->getValue( 'F_contact_geo_location_id' ) );
		}

		return parent::loadFullDetails();
	}

	/**
	 * Overwritten toArray
	 *
	 * @return string[]
	 */
	public function toArray() {
		$rVal = parent::toArray();

		$this->loadFullDetails();

		if ( $this->icon !== null ) {
			/** @noinspection PhpUndefinedMethodInspection */
			$rVal[ 'icon' ] = $this->icon->toArray();
		}
		if ( $this->geoLocation !== null ) {
			/** @noinspection PhpUndefinedMethodInspection */
			$rVal[ 'geo_location' ] = $this->geoLocation->toArray();
		}

		$return = [ 'type' => $this->getShortClassName(), 'object' => $rVal ];

		return $return;
	}

	/**
	 * store
	 *
	 * @param boolean $forceOverwritePrimaryKeys
	 * @return boolean|int
	 */
	public function store( $forceOverwritePrimaryKeys = false ) {

		if ( $this->new ) {
			$this->setValue( 'contact_created', ( new \DateTime() )->format( 'Y-m-d H:i:s' ) );
		}

		if ( !$this->getValue( 'contact_phone' ) ) {
			$this->setValue( 'contact_phone', null );
		}

		$rVal = parent::store( $forceOverwritePrimaryKeys );

		return $rVal;
	}

	/**
	 * getGeoLocation
	 *
	 * @return \wplibs\dbinterface\GeoLocation|null
	 */
	public function getGeoLocation() {
		$this->loadFullDetails();

		return $this->geoLocation;
	}
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
