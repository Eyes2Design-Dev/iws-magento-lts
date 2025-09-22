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

class AddInMage_DexConditions_Model_SalesRule_Rule extends Mage_SalesRule_Model_Rule
{
	/**
	 * Validates data for rule
	 * @param Varien_Object $object
	 * @returns boolean|array - returns true if validation passed successfully. Array with error
	 * description otherwise
	 */
	public function validateData(Varien_Object $object)
	{
		$errors = array();
		
		if($object->getData('extra')){
			$extra = $object->getData('extra');	
			
			$dexWeekIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Week');
			$dexDayIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Day');
			$dexSalesIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Sales');
			$dexCustomerIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Customer');
			
			// Weekly
			if(isset($extra['use_weekly_behavior']) && $extra['use_weekly_behavior'] == true && $dexWeekIncl)
				$errors = array_merge($errors, Mage::helper('dexconditions/types_week')->isValidSettings($extra, $onSave = true));
			
			// Time Frames
			if(isset($extra['use_daily_behavior']) && $extra['use_daily_behavior'] == true && $dexDayIncl) 
				if(isset($extra['timeframe']) && count($extra['timeframe'])) 
					foreach ($extra['timeframe'] as $timeframeSet) 
						$errors = array_merge($errors, Mage::helper('dexconditions/types_day')->isValidCondition($timeframeSet, $onSave = true));
				
	
			// Sales
			if(isset($extra['use_sales_behavior']) && $extra['use_sales_behavior'] == true && $dexSalesIncl) 
				if(isset($extra['sales']) && count($extra['sales'])) 
					foreach ($extra['sales'] as $salesSet) 
						$errors = array_merge($errors, Mage::helper('dexconditions/types_sales')->isValidCondition($salesSet, $onSave = true));
				

			// Customers
			if(isset($extra['use_customer_behavior']) && $extra['use_customer_behavior'] == true && $dexCustomerIncl) 
				if(isset($extra['customer']) && count($extra['customer'])) 
					foreach ($extra['customer'] as $customerSet) 
						$errors = array_merge($errors, Mage::helper('dexconditions/types_customer')->isValidCondition($customerSet, $onSave = true));
				
			
		}

		if($object->getData('from_date') && $object->getData('to_date')){
			$dateStart = new Zend_Date($object->getData('from_date'), Varien_Date::DATE_INTERNAL_FORMAT);
			$dateEnd = new Zend_Date($object->getData('to_date'), Varien_Date::DATE_INTERNAL_FORMAT);
			
			if ($dateStart->compare($dateEnd)===1) {
				$errors[] = Mage::helper('dexconditions')->__("Please check 'General Information' tab. End Date should be greater than Start Date.");
			}
		}
		
// 		Zend_Debug::dump(array_unique($errors));
// 		die();
		return (count($errors)) ? array_unique($errors) : true;
	}
}
