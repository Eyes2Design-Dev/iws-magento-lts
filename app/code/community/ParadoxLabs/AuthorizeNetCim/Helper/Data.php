<?php
/**
 * Authorize.Net CIM - Helper methods
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

class ParadoxLabs_AuthorizeNetCim_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Define the authnetcim tab in admin customer edit.
	 */
	public function tab() {
		return array(
			'label'     => $this->__('Authorize.Net CIM'),
			'class'     => 'ajax',
			'url'       => Mage::getUrl('*/authnetcim/manage', array('_current' => true))
		);
	}
}
