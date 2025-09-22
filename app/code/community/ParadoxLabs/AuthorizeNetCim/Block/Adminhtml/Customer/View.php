<?php
/**
 * Authorize.Net CIM - Customer card manager - Wrapper and card list
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
 * @category    ParadoxLabs
 * @package     ParadoxLabs_AuthorizeNetCim
 * @author      Ryan Hoerr <ryan@paradoxlabs.com>
 */

class ParadoxLabs_AuthorizeNetCim_Block_Adminhtml_Customer_View extends Mage_Adminhtml_Block_Template
{
	public function _construct() {
		parent::_construct();
		
		$this->setTemplate( 'authorizenetcim/manage.phtml' );
		
		$customer = Mage::getModel('customer/customer')->load( $this->getRequest()->getParam('id') );
		$this->setCustomer( $customer );
		
		$payment = Mage::getModel('authnetcim/payment');
		$payment->setCustomer( $this->getCustomer() )
				->setStore( $this->getCustomer()->getStore()->getId() );
		$this->setPayment( $payment );
	}
    
    public function isAjax()
    {
    	return $this->getRequest()->getParam('isAjax');
    }
}
