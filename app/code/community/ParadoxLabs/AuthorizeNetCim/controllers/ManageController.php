<?php
/**
 * Authorize.Net CIM - 'Manage My Cards' controller.
 *
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Having a problem with the plugin?
 * Not sure what something means?
 * Need custom development?
 * Give us a call!
 *
 * @category	ParadoxLabs
 * @package		ParadoxLabs_AuthorizeNetCim
 * @author		Ryan Hoerr <ryan@paradoxlabs.com>
 */

class ParadoxLabs_AuthorizeNetCim_ManageController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch() {
		parent::preDispatch();

		if( !Mage::getSingleton('customer/session')->authenticate($this) ) {
			$this->getResponse()->setRedirect( Mage::helper('customer')->getLoginUrl() );
			$this->setFlag( '', self::FLAG_NO_DISPATCH, true );
		}

		return $this;
	}
	
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}
