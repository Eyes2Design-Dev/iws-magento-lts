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

class AddInMage_DexConditions_Model_Observer
{

	public function ruleBeforSave($observer)
	{		
		$rule = $observer->getRule();
		
		if($rule->getExtra()) {	
			$extra = $rule->getExtra();	
	
			$dexDayValidator = @class_exists('AddInMage_DexConditions_Helper_Validation_Day');
			$dexSalesValidator = @class_exists('AddInMage_DexConditions_Helper_Validation_Sales');
			$dexCustomerValidator = @class_exists('AddInMage_DexConditions_Helper_Validation_Customer');			
			
			if($dexDayValidator)
				$extra = Mage::helper('dexconditions/validation_day')->filter($extra);
			
			if($dexSalesValidator)
				$extra = Mage::helper('dexconditions/validation_sales')->filter($extra);
			
			if($dexCustomerValidator)
				$extra = Mage::helper('dexconditions/validation_customer')->filter($extra);			
			
			$extra = serialize($extra);
			$rule->setExtraConditions($extra);
		}
		
		return $this;
	}
	
	public function ruleAfterLoad($observer)
	{		
		$rule = $observer->getRule();
		
		$extraArr = unserialize($rule->getExtraConditions());
		
		if (is_array($extraArr) && !empty($extraArr)) $rule->setExtra($extraArr);
		else $rule->setExtra(array());
	}
}