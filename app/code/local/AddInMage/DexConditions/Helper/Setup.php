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

class AddInMage_DexConditions_Helper_Setup extends Mage_Core_Helper_Abstract
{

	public function notifyAdminUser($version)
	{
	
		//$user_guide_url = 'http://add-in-mage.com/resources/discount-extra-conditions/user-guide/';
		//$change_log_url = 'http://add-in-mage.com/resources/discount-extra-conditions/change-log/';
	
		try {
			
	
			Mage::getModel('adminnotification/inbox')->setSeverity(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE)
			->setTitle($this->__('Discount Conditions extension (%s) is installed successfully. The current version is %s.', $this->getDexEdition(), $version))
			->setDateAdded(gmdate('Y-m-d H:i:s'))
			->setUrl(false)
// 			->setDescription($this->__('You have just installed "Discount Conditions" extension. Please see the <a href="%s">change log</a> and the <a href="%s">user guide</a> for information about configuration.', $change_log_url, $user_guide_url))
			->setDescription($this->__('You have just installed "Discount Conditions" extension.'))
			->save();
	
		} catch (Exception $e) {
				
			Mage::helper('dexconditions')->decLog(__METHOD__, $e);
		}
	}
	
	public function getDexEdition() 
	{
		$dexWeekIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Week');
		$dexDayIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Day');
		$dexSalesIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Sales');
		$dexCustomerIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Customer');
		
		$editions = array();
		
		if($dexWeekIncl && $dexDayIncl && $dexSalesIncl && $dexCustomerIncl)
			return $this->__('Collection edition');
		else {
			if($dexWeekIncl) $editions[] = $this->__('Weekly scheduler');
			if($dexDayIncl) $editions[] = $this->__('Daily scheduler');
			if($dexSalesIncl) $editions[] = $this->__('Sales');
			if($dexCustomerIncl) $editions[] = $this->__('Customer');
			
			if(count($editions)) {
				if(count($editions) == 1) {
					return $this->__('%s edition',$editions[0]); 
				} else {
					$edition = '';
					$last_key = end(array_keys($editions));
					foreach ($editions as $key => $ed) {
						if ($key == $last_key) {
							$edition .= $ed .' '. $this->__('editions');
						} else {
							$edition .= $ed .', ';
						}
					}
					
					return $edition;
				}
			}				
		}		
	}
	
	public function applyVersionChanges($version = null)
	{

	}
}