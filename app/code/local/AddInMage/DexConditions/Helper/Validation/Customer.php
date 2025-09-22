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

class AddInMage_DexConditions_Helper_Validation_Customer extends Mage_Core_Helper_Abstract
{
		
	public function filter($extra)
	{
		
	    
		if(isset($extra['use_customer_behavior']) && $extra['use_customer_behavior'] == true) {
			if(isset($extra['customer']) && count($extra['customer'])) {
				sort($extra['customer']);
				foreach ($extra['customer'] as $key => $Crule) {
		
					if(isset($Crule['status_criterion'])) {
						if($Crule['status_criterion'] == 'any') {
							if(isset($Crule['statuses']))
								unset($extra['customer'][$key]['statuses']);
						} else {
							if(isset($Crule['statuses']) && !count($Crule['statuses'])) {
								$extra['customer'][$key]['status_criterion'] = 'any';
								unset($extra['customer'][$key]['statuses']);
							}
						}
					}
		
					if(isset($Crule['item_status_criterion'])) {
						if($Crule['item_status_criterion'] == 'any') {
							if(isset($Crule['item_statuses']))
								unset($extra['customer'][$key]['item_statuses']);
						} else {
							if(isset($Crule['item_statuses']) && !count($Crule['item_statuses'])) {
								$extra['customer'][$key]['item_status_criterion'] = 'any';
								unset($extra['customer'][$key]['item_statuses']);
							}
						}
					}
		
					if(isset($Crule['store_criterion'])) {
						if($Crule['store_criterion'] == 'any') {
							if(isset($Crule['stores']))
								unset($extra['customer'][$key]['stores']);
						} else {
							if(isset($Crule['stores']) && empty($Crule['stores'])) {
								$extra['customer'][$key]['store_criterion'] = 'any';
								unset($extra['customer'][$key]['stores']);
							}
						}
					}
		
					if(isset($Crule['condition'])) {
						if($Crule['condition'] == 'not_purchased' || $Crule['condition'] == 'not_purchased_int') {
							if(isset($Crule['item_status_criterion'])) {
								$extra['customer'][$key]['item_status_criterion'] = 'any';
								unset($extra['customer'][$key]['item_statuses']);
							}
						}
					}
				}
			} else {
				$extra['use_customer_behavior'] = false;
				unset($extra['customer']);
			}
		} else {
			if(isset($extra['customer']))
				unset($extra['customer']);
		}
		
		return $extra;		
	}
}