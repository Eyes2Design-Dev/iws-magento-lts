<?php
/**
 * Add In Mage::
 *
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the EULA that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: http://add-in-mage.com/support/presales/eula/
 *
 *
 * PROPRIETARY DATA
 * 
 * This file contains trade secret data which is the property of Add In Mage:: Ltd. 
 * This file is submitted to recipient in confidence.
 * Information and source code contained herein may not be used, copied, sold, distributed, 
 * sub-licensed, rented, leased or disclosed in whole or in part to anyone except as permitted by written
 * agreement signed by an officer of Add In Mage:: Ltd.
 * 
 * 
 * MAGENTO EDITION NOTICE
 * 
 * This software is designed for Magento Community edition.
 * Add In Mage:: Ltd. does not guarantee correct work of this extension on any other Magento edition.
 * Add In Mage:: Ltd. does not provide extension support in case of using a different Magento edition.
 * 
 * 
 * @category    AddInMage
 * @package     AddInMage_DexConditions
 * @copyright   Copyright (c) 2013 Add In Mage:: Ltd. (http://www.add-in-mage.com)
 * @license     http://add-in-mage.com/support/presales/eula/  End User License Agreement (EULA)
 * @author      Add In Mage:: Team <team@add-in-mage.com>
 */

class AddInMage_DexConditions_Model_SalesRule_Validator extends Mage_SalesRule_Model_Validator
{
	/**
	 * Init validator
	 * Init process load collection of rules for specific website,
	 * customer group and coupon code
	 *
	 * @param   int $websiteId
	 * @param   int $customerGroupId
	 * @param   string $couponCode
	 * @return  AddInMage_DexConditions_Model_SalesRule_Validator
	 */
	public function init($websiteId, $customerGroupId, $couponCode)
	{
		 
		$this->setWebsiteId($websiteId)
		->setCustomerGroupId($customerGroupId)
		->setCouponCode($couponCode);
	
		$key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
		if (!isset($this->_rules[$key])) {
			$rules = Mage::getResourceModel('salesrule/rule_collection')->setValidationFilter($websiteId, $customerGroupId, $couponCode);
	
			if(Mage::helper('dexconditions')->useAdvancedConditions()) {
				 
				$tmp_rules = clone $rules;
				$nin = Mage::helper('dexconditions')->getNinIds($tmp_rules);
			
				$this->_rules[$key] = (count($nin)) ? $rules->addFieldToFilter('main_table.rule_id', array('nin' => $nin))->load() : $rules->load();
			}
	
			else
				$this->_rules[$key] = $rules->load();
		}

		return $this;
	}
}
