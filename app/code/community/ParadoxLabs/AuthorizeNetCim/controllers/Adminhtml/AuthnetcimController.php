<?php
/**
 * Authorize.Net CIM - Customer card manager controller
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

class ParadoxLabs_AuthorizeNetCim_Adminhtml_AuthnetcimController extends Mage_Adminhtml_Controller_Action
{
	public function editAction() {
		echo Mage::app()->getLayout()->createBlock('authnetcim/adminhtml_customer_edit')->toHtml();
	}
	
	public function saveAction() {
		echo Mage::app()->getLayout()->createBlock('authnetcim/adminhtml_customer_view')->toHtml();
	}
	
	public function emptyAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function manageAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function reloadCardsAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}
