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

class AddInMage_DexConditions_Helper_Types_Customer extends Mage_Core_Helper_Abstract
{
	const ENABLE_DISCOUNT 	= 1;
	const DISABLE_DISCOUNT 	= 0;
	
	const NON_CALC_IGNORE	= 1;
	const NON_CALC_DISABL 	= 2;
	const NON_CALC_TRUE 	= 3;
	const NON_CALC_FALSE 	= 4;
	
	const ALL_TRUE 			= 'all_true';
	const ALL_FALSE 		= 'all_false';
	const ANY_TRUE 			= 'any_true';
	const ANY_FALSE 		= 'any_false';
	
	const NO_VALUE 			= 2;
	
	private function getCustomerData($data) 
	{	
	    $adminhtml = false;
	    $session = Mage::getSingleton('customer/session');
	    
	    $result = null;
	    
	    if(Mage::getSingleton('adminhtml/session_quote') && Mage::getSingleton('adminhtml/session_quote')->getCustomerId()) {   
	        $session = Mage::getSingleton('adminhtml/session_quote');
	        $adminhtml = true;
	    }
	    
	    $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
	    
	    switch ($data) {
	        case 'id':
	            $result = $session->getCustomerId();
	            break;
	        case 'first_name':
	            $result = ($adminhtml) ? $customer->getFirstname() : $session->getCustomer()->getFirstname();
	            break;
	        case 'last_name':
	            $result = ($adminhtml) ? $customer->getLastname() : $session->getCustomer()->getLastname();
	            break;
	        case 'sex':
	            $result = ($adminhtml) ? $customer->getGender() : $session->getCustomer()->getGender();
	            break;
	        case 'group':
	            $result = ($adminhtml) ? $session->getCustomer()->getGroupId() : $session->getCustomerGroupId();
	            break;
	        case 'email':
	            $result = ($adminhtml) ? $customer->getEmail() : $session->getCustomer()->getEmail();
	            break;
	        case 'birthday':
	            $result = ($adminhtml) ? $customer->getDob() : $session->getCustomer()->getDob();
	            break;
	        case 'account_date':
	            $result = ($adminhtml) ? $customer->getCreatedAt() : $session->getCustomer()->getCreatedAt();
	            break;
	        default:
	            $result = ($adminhtml) ? $customer->getData($data) : $session->getCustomer()->getData($data);
	    }
	    
	    return $result;
	}
	
	public function getConditionNinId($extra, $ruleId)
	{
        
	    $area = Mage::getDesign()->getArea();	    
	    	    
		if(isset($extra['use_customer_behavior']) && (bool)$extra['use_customer_behavior'] == true) {
		    if($area !== 'adminhtml') {
		        
		        if(Mage::app()->getRequest()->getControllerName() == 'cart' && Mage::app()->getRequest()->getActionName() == 'index')
        			if(isset($extra['force_customer_auth']) && (bool)$extra['force_customer_auth'] == true) {
        				if (! Mage::getSingleton('customer/session')->isLoggedIn()) {
        					Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        	
        					Mage::getSingleton('customer/session')->addNotice($this->__('Please log in or register to be able to apply special discounts to your cart (if any).'));
        					Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login', array('_secure' => true)))->sendResponse();
        				}
        			} elseif(!isset($extra['force_customer_auth']) || (isset($extra['force_customer_auth']) && (bool)$extra['force_customer_auth'] == false)) {
        			    
        				if (! Mage::getSingleton('customer/session')->isLoggedIn()) {
        				    $accountLink = Mage::getUrl('customer/account/login', array('_secure' => true));
        				    $linkText = $this->__('log in or register');
        				    $link = '<a href="'. $accountLink .'">'.$linkText.'</a>';
        				    
        				    Mage::getSingleton('core/session')->addNotice($this->__('Please %s to be able to apply special discounts to your cart (if any).', $link));
        					return 'not_logged_in';
        				}
        			}
		    }
			// Validate settings
			if(!$this->isValidSettings($extra)) return 'bad_rule';		
			
			$matchRuleSC = false;
			$ruleSC = $extra['customer_condition'];
			$action = $extra['customer_action'];
			$nonCalcAction = $extra['noncacl_action'];
			$results = array();
			
			foreach ($extra['customer'] as $condition) {	
				// Validate condition
// 				Zend_Debug::dump($condition);
				if(!$this->isValidCondition($condition)) continue;
				$results[] = $this->checkCondition($condition, $nonCalcAction);
			}
			
			if($nonCalcAction == self::NON_CALC_DISABL) {
				if(in_array(self::NO_VALUE, $results))
					return $ruleId;
			}
			
			if($ruleSC == self::ALL_TRUE) {
				$matchRuleSC = (in_array(0, $results)) ? false : true;
			} elseif($ruleSC == self::ALL_FALSE) {
				$matchRuleSC = (in_array(1, $results)) ? false : true;
			} elseif($ruleSC == self::ANY_TRUE) {
				$matchRuleSC = (in_array(1, $results)) ? true : false;
			} elseif($ruleSC == self::ANY_FALSE) {
				$matchRuleSC = (in_array(0, $results)) ? true : false;
			}

// 			Zend_Debug::dump($results, 'RESULT ======');
// 			die();
			
			if($action == self::ENABLE_DISCOUNT && !$matchRuleSC)
				return $ruleId;
			if($action == self::DISABLE_DISCOUNT && $matchRuleSC) 
				return $ruleId;
		}
		
		return null;
	}
	
	protected function getCartItemPastConditions()
	{
	    return array('cart_items_past_orders_all', 'cart_items_past_orders_any', 'cart_items_past_orders_int_all', 'cart_items_past_orders_int_any');
	}
	
protected function checkCondition($condition, $nonCalcAction)
	{
		$calcValue = $this->calculate($condition);
		$origValue = $this->getInput($condition);
		
		if($calcValue == 'no_value') {
			if($nonCalcAction == self::NON_CALC_TRUE) return 1;
			elseif($nonCalcAction == self::NON_CALC_FALSE) return 0;
			else return self::NO_VALUE;
		}
		
		$today = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));		
		
		$cartItems = array();
		
		if(in_array($condition['criterion'], $this->getCartItemPastConditions())) {
		    $cart = Mage::getModel('checkout/cart')->getQuote();

		    foreach ($cart->getAllItems() as $item) {
		        $cartItems[] = $item->getProductId();
		    }

// 		    Zend_Debug::dump($cartItems, 'in cart ======');
		    
		}
		
// 		Zend_Debug::dump($calcValue, 'calculated ======');
// 		Zend_Debug::dump($origValue, 'entered ======');
		$result = 0;
	
		switch ($condition['condition']) {
			case 'is_na':
				$result = ($calcValue && !is_null($calcValue)) ? 1 : 0;
				break;				
			case 'is_not_na':
				$result = (is_null($calcValue)) ? 1 : 0;
				break;				
			case 'is_one':
				$result = (in_array($calcValue, $origValue)) ? 1 : 0;
				break;				
			case 'is_not_one':
				$result = (!in_array($calcValue, $origValue)) ? 1 : 0;
				break;				
			case 'reviewed':
				$all = count(array_intersect($origValue, $calcValue)) == count($origValue);
				$result = ($all) ? 1 : 0;
				break;				
			case 'not_reviewed':
				$all = count(array_intersect($origValue, $calcValue)) == count($origValue);
				$result = (!$all) ? 1 : 0;
				break;				
			case 'purchased':
			case 'purchased_int':
				if($condition['criterion'] == 'specific_product_all' || $condition['criterion'] == 'specific_product_int_all') {
					$all = count(array_intersect($origValue, $calcValue)) == count($origValue);
					$result = ($all) ? 1 : 0;
				} elseif($condition['criterion'] == 'specific_product_any' || $condition['criterion'] == 'specific_product_int_any') {
					$all = array_intersect($origValue, $calcValue);
					$result = (count($all)) ? 1 : 0;
				} elseif($condition['criterion'] == 'cart_items_past_orders_all' || $condition['criterion'] == 'cart_items_past_orders_int_all') {  	

				    $cartItemsMatch = count(array_intersect($origValue, $cartItems)) == count($origValue);
                    //Zend_Debug::dump($cartItemsMatch, 'cart items match ======');
                    
				    if($cartItemsMatch) {		
				        		        
				        $all = count(array_intersect($cartItems, $calcValue)) == count($cartItems);
				        $result = ($all) ? 1 : 0;
				        				        
				    } else {
				        $result = 0;
				    }
				 
				} elseif($condition['criterion'] == 'cart_items_past_orders_any' || $condition['criterion'] == 'cart_items_past_orders_int_any') {

				    $cartItemsMatch = array_intersect($origValue, $cartItems);
				    //Zend_Debug::dump($cartItemsMatch, 'cart items match ======');
				    
				    if(count($cartItemsMatch)) {
				        $all = array_intersect($cartItemsMatch, $calcValue);
				        $result = (count($all)) ? 1 : 0;				        
				    } else {
				        $result = 0;
				    }
				}
				break;				
			case 'not_purchased':
			case 'not_purchased_int':
				if($condition['criterion'] == 'specific_product_all' || $condition['criterion'] == 'specific_product_int_all') {
					$all = count(array_intersect($origValue, $calcValue)) == count($origValue);
					$result = (!$all) ? 1 : 0;
				} elseif($condition['criterion'] == 'specific_product_any' || $condition['criterion'] == 'specific_product_int_any') {
					$all = array_intersect($origValue, $calcValue);
					$result = (!count($all)) ? 1 : 0;
				} elseif($condition['criterion'] == 'cart_items_past_orders_all' || $condition['criterion'] == 'cart_items_past_orders_int_all') {
				    
				    $cartItemsMatch = count(array_intersect($origValue, $cartItems)) == count($origValue);
				    //Zend_Debug::dump($cartItemsMatch, 'cart items match ======');
				    
				    if($cartItemsMatch) {
				    
				        $all = count(array_intersect($cartItems, $calcValue)) == count($cartItems);
				        $result = (!$all) ? 1 : 0;
				    
				    } else {
				        $result = 0;
				    }
				    
				} elseif($condition['criterion'] == 'cart_items_past_orders_any' || $condition['criterion'] == 'cart_items_past_orders_int_any') {
				    
				    $cartItemsMatch = array_intersect($origValue, $cartItems);
				    //Zend_Debug::dump($cartItemsMatch, 'cart items match ======');
				    
				    if(count($cartItemsMatch)) {
				        $all = array_intersect($cartItemsMatch, $calcValue);
				        $result = (!count($all)) ? 1 : 0;				        
				    } else {
				        $result = 0;
				    }
				}
				break;				
			case 'yesterday':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ($calcDate->isYesterday()) ? 1 : 0;
				break;				
			case 'today':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ($calcDate->isToday()) ? 1 : 0;
				break;
			case 'tomorrow':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ($calcDate->isTomorrow()) ? 1 : 0;
				break;				
			case 'previous_week':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::WEEK) == (int)$today->subWeek(1)->get(Zend_Date::WEEK)) ? 1 : 0;
				break;				
			case 'this_week':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::WEEK) == (int)$today->get(Zend_Date::WEEK)) ? 1 : 0;
				break;				
			case 'next_week':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::WEEK) == (int)$today->addWeek(1)->get(Zend_Date::WEEK)) ? 1 : 0;
				break;				
			case 'this_month':
			    $calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::MONTH) == (int)$today->get(Zend_Date::MONTH)) ? 1 : 0;
				break;				
			case 'previous_month':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::MONTH) == (int)$today->subMonth(1)->get(Zend_Date::MONTH)) ? 1 : 0;				
				break;				
			case 'next_month':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::MONTH) == (int)$today->addMonth(1)->get(Zend_Date::MONTH)) ? 1 : 0;
				break;				
			case 'this_year':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::YEAR) == (int)$today->get(Zend_Date::YEAR)) ? 1 : 0;				
				break;				
			case 'previous_year':
				$calcDate = (is_object($calcValue) && $calcValue instanceof Zend_Date) ? $calcValue : new Zend_Date($calcValue);
				$result = ((int)$calcDate->get(Zend_Date::YEAR) == (int)$today->subYear(1)->get(Zend_Date::YEAR)) ? 1 : 0;
				break;				
			case '==':
				$result = ($calcValue == $origValue) ? 1 : 0;
				break;				
			case '!=':
				$result = ($calcValue != $origValue) ? 1 : 0;
				break;
			case '>=':
			case '>=d':
				$result = ($calcValue >= $origValue) ? 1 : 0;
				break;
			case '<=':
			case '<=d':
				$result = ($calcValue <= $origValue) ? 1 : 0;
				break;			
			case '>':
				$result = ($calcValue > $origValue) ? 1 : 0;
				break;			
			case '<':
				$result = ($calcValue < $origValue) ? 1 : 0;
				break;			
			case 'sub':
				$result = ($calcValue == 1) ? 1 : 0;
				break;			
			case 'notsub':
				$result = ($calcValue == 2) ? 1 : 0;
				break;			
		}
		//Zend_Debug::dump($result, 'result ======');
		return $result;
	}
	
	protected function calculate($condition)
	{
		
		$subject = $condition['criterion'];
		
		$customerId = $this->getCustomerData('id');
		
		$value = 'no_value';
		
		if($this->getSalesConditions($subject)) {
			
			$stores = $this->getStores($condition);
			$statuses = $this->getStatuses($condition, true);
			$value = '0';
			
			$customerOrders = Mage::getModel('dexconditions/customer_sales_report_order_collection');
			$customerOrders->addCustomerFilter((int)$customerId);
			$customerOrders->addOrderStatusFilter($statuses);
			
			if($stores)
			$customerOrders->addStoreFilter($stores);
			
			
			if(strrpos($subject, '_int') != 0) {
				$dateRange = $this->getDateRange($condition);
				$customerOrders->setDateRange($dateRange['from'], $dateRange['to']);
			}
			
			$result = $customerOrders->loadCustomerData();			
			
			$subjectData = (strrpos($subject, '_int') != 0) ? substr($subject, 0, -4) : $subject;			

			if(count($result)) {
				$value = $result[0][$subjectData];
			}			
			
		}
		
		switch ($subject){
			case 'customer_specific':
				$id = $customerId;
				if($id)
					$value = $id;
				break;
			case 'orders_av_amount':
			case 'orders_av_amount_int':
				$stores = $this->getStores($condition);
				$statuses = $this->getStatuses($condition);
				
				$value = '0';
				
				$customerOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);				
				$customerOrders->addAttributeToFilter('state', array('nin' => array(Mage_Sales_Model_Order::STATE_CANCELED)));
				
				if(!is_null($stores))
					$customerOrders->addAttributeToFilter('store_id', array('in' => $stores));
				
				if(!is_null($statuses))
					$customerOrders->addAttributeToFilter('status', array('in' => $statuses));
			
				if($subject == 'orders_av_amount_int') {
					$dateRange = $this->getDateRange($condition);
					$customerOrders->addAttributeToFilter('updated_at', array('from' => $dateRange['from'], 'to' => $dateRange['to']));
				}
							
				
				if($customerOrders->getSize()) {
					
					$orderTotals = $customerOrders->getColumnValues('base_grand_total');
					$value = array_sum($orderTotals)/($customerOrders->getSize());
				}

				break;
			case 'customer_first_name':
				$firstName = $this->getCustomerData('first_name');
				if($firstName)
					$value = $firstName;
				break;
			case 'customer_last_name':
				$lastName = $this->getCustomerData('last_name');
				if($lastName)
					$value = $lastName;
				break;
			case 'customer_sex':
				$gender = $this->getCustomerData('sex');
				
				if($gender && !is_null($gender))
					$value = $gender;
				elseif(is_null($gender))
					$value = '0';
				break;
			case 'customer_group':
				$groupId = $this->getCustomerData('group');
				if($groupId && !is_null($groupId))
					$value = $groupId;
				break;
			case 'customer_newsletter':
				$value = 2;
				
				$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($this->getCustomerData('email'));
				if($subscriber->getId() && !is_null($subscriber->getId())) {
					if($subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
						$value = 1;
				}
				break;
			case 'customer_birthday':
				
		        $dob = $this->getCustomerData('birthday');
				
		        if ($dob && ! is_null($dob)) {
                        
                    $bd = Mage::getModel('core/date')->gmtDate('Y-m-d', $dob);
                    $bdArray = explode('-', $bd);
                    
                    $bdDate = new Zend_Date();
                    $bdDate->setDay($bdArray[2]);
                    $bdDate->setMonth($bdArray[1]);
                    $bdDate->setYear($bdArray[0]);
                    
                    $value = $bdDate;
                } elseif (is_null($dob)) {
                    if ($condition['condition'] == 'is_na' || $condition['condition'] == 'is_not_na')
                        $value = null;
                }
				
				break;
			case 'customer_account_date':
				$date = $this->getCustomerData('account_date');

				if($date) 				
					$value = Mage::getModel('core/date')->date('Y-m-d', $date);
				
				break;
			case 'review_last_date':			   

				$reviewsCollection = Mage::getModel('review/review')->getProductCollection()
										->addStoreFilter(Mage::app()->getStore()->getId())
            							->addCustomerFilter($customerId)
            							->setDateOrder();
				
				if($reviewsCollection->getSize()) {
				    $value = Mage::getModel('core/date')->date('Y-m-d', $reviewsCollection->getFirstItem()->getCreatedAt());
				}

				break;
			case 'review_count':

				$reviewsCollection = Mage::getModel('review/review')->getProductCollection()
										->addStoreFilter(Mage::app()->getStore()->getId())
            							->addCustomerFilter($customerId)
            							->setDateOrder();
				
				if($reviewsCollection->getSize()) {
					$value = $reviewsCollection->getSize();
					
				} 
				else $value = '0';
				
				break;
			case 'review_products':

				$reviewsCollection = Mage::getModel('review/review')->getProductCollection()
										->addStoreFilter(Mage::app()->getStore()->getId())
            							->addCustomerFilter($customerId);
				
				if($reviewsCollection->getSize()) {
					$value = $reviewsCollection->getColumnValues('entity_pk_value');
				} 
				else $value = array();
				
				break;
			case 'next_order_number':
				$stores = $this->getStores($condition);

				$customerOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);			            
				
				if(!is_null($stores))
					$customerOrders->addAttributeToFilter('store_id', array('in' => $stores));
			
				$value = $customerOrders->getSize() + 1;			
			
				break;
				
			case 'specific_product_all':
			case 'specific_product_any':
			case 'specific_product_int_all':
			case 'specific_product_int_any':
				$stores = $this->getStores($condition);
				$itemStatuses = $this->getItemStatuses($condition);
				
				$customerOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);
						            
				if(!is_null($stores))
					$customerOrders->addAttributeToFilter('store_id', array('in' => $stores));
			
				if($subject == 'specific_product_int_all' || $subject == 'specific_product_int_any') {
					$dateRange = $this->getDateRange($condition);
					$customerOrders->addAttributeToFilter('updated_at', array('from' => $dateRange['from'], 'to' => $dateRange['to']));
				}
				
				if($customerOrders->getSize()) {
					$orderIds = $customerOrders->getColumnValues('entity_id');
					
					$items = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id', array('in' => $orderIds));
					
					if(is_null($itemStatuses)) {
						$purchasedProductIds = $items->getColumnValues('product_id');
						$value = (count($purchasedProductIds)) ? array_values(array_unique($purchasedProductIds)) : array();
					} else {
						$purchasedProductIds = array();
						foreach ($items as $item)
							if(in_array($item->getStatusId(), $itemStatuses)) $purchasedProductIds[] = $item->getProductId();
						
						$value = (count($purchasedProductIds)) ? array_values(array_unique($purchasedProductIds)) : array();
					}
				} else $value = array();

				break;
				
			case 'cart_items_past_orders_any':
			case 'cart_items_past_orders_all':
			case 'cart_items_past_orders_int_any':
			case 'cart_items_past_orders_int_all':
				$stores = $this->getStores($condition);
				$itemStatuses = $this->getItemStatuses($condition);
				
				$customerOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);
						            
				if(!is_null($stores))
					$customerOrders->addAttributeToFilter('store_id', array('in' => $stores));
			
				if($subject == 'specific_product_int_all' || $subject == 'specific_product_int_any') {
					$dateRange = $this->getDateRange($condition);
					$customerOrders->addAttributeToFilter('updated_at', array('from' => $dateRange['from'], 'to' => $dateRange['to']));
				}
				
				if($customerOrders->getSize()) {
					$orderIds = $customerOrders->getColumnValues('entity_id');
					
					$items = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id', array('in' => $orderIds));
					
					if(is_null($itemStatuses)) {
						$purchasedProductIds = $items->getColumnValues('product_id');
						$value = (count($purchasedProductIds)) ? array_values(array_unique($purchasedProductIds)) : array();
					} else {
						$purchasedProductIds = array();
						foreach ($items as $item)
							if(in_array($item->getStatusId(), $itemStatuses)) $purchasedProductIds[] = $item->getProductId();
						
						$value = (count($purchasedProductIds)) ? array_values(array_unique($purchasedProductIds)) : array();
					}
				} else $value = array();

				break;
			case 'specific_cat':
			case 'specific_cat_int':
			    
			    $itemStatuses = $this->getItemStatuses($condition);
			    $customerOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);
			    
			    if($subject == 'specific_cat_int') {
    			    $dateRange = $this->getDateRange($condition);
    			    $customerOrders->addAttributeToFilter('updated_at', array('from' => $dateRange['from'], 'to' => $dateRange['to']));
			    }
			  
			    
			    $category = $condition['category'];
			    $categoryData = explode('/', $category);
			    $categoryId = $categoryData[1];
			    
			    $value = '0';
			    
			    if($customerOrders->getSize()) {
			        $orderIds = $customerOrders->getColumnValues('entity_id');
			        $items = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id', array('in' => $orderIds));
			        
			        $productIds = $items->getColumnValues('product_id');
			        $productIds = array_unique($productIds);
			        
			        $products = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('entity_id', array('in' => $productIds))->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId)); 
			        $productIds = array_unique($products->getColumnValues('entity_id'));
			        
			        $items = $items->clear()->addAttributeToFilter('product_id', array('in' => $productIds))->load();
			        
			        foreach ($items as $item) {
			            
			            if(is_null($itemStatuses)) 
			                $value += $item->getData('base_row_total_incl_tax');
			            
			            else 
			                if(in_array($item->getStatusId(), $itemStatuses)) $value += $item->getData('base_row_total_incl_tax');
			            	            
			        }
			        
			    } else $value = '0';
			    
		}
		
		return $value;	
	}
	
	protected function getStatuses($condition, $salesR = false)
	{
		$statuses = null;
		if($salesR) $statuses = $this->getOrderStatuses();
		
		$statusCrit = (isset($condition['status_criterion'])) ? $condition['status_criterion'] : 'any';
	
		if($statusCrit == 'specified' && isset($condition['statuses'])) {
			if(is_array($condition['statuses']) && count($condition['statuses'])){
				$statuses = $condition['statuses'];
			}
		}
	
		return $statuses;
	}
	
	protected function getStores($condition)
	{
		$storeCrit = (isset($condition['store_criterion'])) ? $condition['store_criterion'] : 'any';
		$stores = null;
	
		if($storeCrit == 'specified' && isset($condition['stores']) && !empty($condition['stores'])) {
			$stores = explode(',', $condition['stores']);
		}
	
		return $stores;
	}
	
	protected function getItemStatuses($condition)
	{
		$itemStatusCrit = (isset($condition['item_status_criterion'])) ? $condition['item_status_criterion'] : 'any';
		$statuses = null;
	
		if($itemStatusCrit == 'specified' && isset($condition['item_statuses']) && is_array($condition['item_statuses']) && count($condition['item_statuses'])) {
			$statuses = $condition['item_statuses'];
		}
	
		return $statuses;
	}
	
	protected function getInput($condition)
	{
		$input = null;
		$subject = $condition['criterion'];
		
		//Processing big lists
		if($this->getSalesConditions($subject))
			$input = (int)$condition['qa'];
		
		switch ($subject){
			case 'customer_specific':
				$input = explode(',', $condition['customer']);
				$input = array_map('trim', $input);
				break;
			case 'review_products':
			case 'specific_product_all':
			case 'specific_product_any':
			case 'specific_product_int_all':
			case 'specific_product_int_any':
			case 'cart_items_past_orders_any':
			case 'cart_items_past_orders_all':
			case 'cart_items_past_orders_int_any':
			case 'cart_items_past_orders_int_all':
				$input = explode(',', $condition['products']);
				$input = array_map('trim', $input);
				break;
			case 'customer_first_name':
			case 'customer_last_name':
				$input = $condition['data'];
				break;
			case 'review_count':
			case 'next_order_number':
			case 'orders_av_amount':
			case 'orders_av_amount_int':
			case 'specific_cat':
			case 'specific_cat_int':
				$input = (int)$condition['qa'];
				break;
			case 'customer_sex':
				$input = $condition['gender'];
				break;
			case 'customer_group':
				$input = $condition['group'];
				break;
			case 'customer_group':
				$input = null;
				break;
			case 'customer_birthday':
			case 'review_last_date':
			case 'customer_account_date':
				if($condition['date']) {
					$data = array('date' => $condition['date']);
					$dataFormatted = $this->_filterDates($data, array('date'));
					$input = Mage::getModel('core/date')->date('Y-m-d', $dataFormatted['date']);
				}
				break;
			
		}
		
		return $input;
	}
	
	protected function _filterDates($array, $dateFields)
	{
		if (empty($dateFields)) {
			return $array;
		}
		$filterInput = new Zend_Filter_LocalizedToNormalized(array(
				'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));
		$filterInternal = new Zend_Filter_NormalizedToLocalized(array(
				'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
		));
	
		foreach ($dateFields as $dateField) {
			if (array_key_exists($dateField, $array) && !empty($dateField)) {
				$array[$dateField] = $filterInput->filter($array[$dateField]);
				$array[$dateField] = $filterInternal->filter($array[$dateField]);
			}
		}
		return $array;
	}
	
	protected function getDateRange($condition)
	{
		$dateEnd = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
		$dateStart = clone $dateEnd;
		
		$dateEnd->setHour(23);
		$dateEnd->setMinute(59);
		$dateEnd->setSecond(59);
		
		$dateStart->setHour(0);
		$dateStart->setMinute(0);
		$dateStart->setSecond(0);		
		
		$shift = (isset($condition['interval'])) ? $condition['interval'] : null;
		$date = array('from' => null, 'to' => null);
		
		switch ($condition['for']) {
			case 'hour':
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				$date = array('from' => $now->setMinute(0)->setSecond(0)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'previous_hour':
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				$hourEnd = clone $now;
				$date = array('from' => $now->setMinute(0)->setSecond(0)->subHour(1)->toString('YYYY-MM-dd HH:m:s'), 'to' => $hourEnd->setMinute(59)->setSecond(59)->subHour(1)->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'today':
				$from = $dateStart->toString('YYYY-MM-dd HH:m:s');
				$date = array('from' => $from, 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'yesterday':
				$yesterday = $dateStart->subDay(1)->toString('YYYY-MM-dd HH:m:s');
				$date = array('from' => $yesterday, 'to' => $dateEnd->subDay(1)->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'week':
				$firstWeekDay = Mage::getStoreConfig('general/locale/firstday', Mage::app()->getStore());
				if(is_null($firstWeekDay)) $firstWeekDay = 1;
				
				$newDate = new Zend_Date();
				$newDate->setYear($dateStart->getYear())
						->setWeek($dateStart->getWeek())
						->set($firstWeekDay, Zend_Date::WEEKDAY_DIGIT)
						->setHour(0)
		 				->setMinute(0)
		 				->setSecond(0);	
				
				$date = array('from' => $newDate->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'month':				
				$newDate = new Zend_Date();
				$newDate->setYear($dateStart->getYear())
						->setMonth($dateStart->getMonth())
						->set(1, Zend_Date::DAY)
						->setHour(0)
		 				->setMinute(0)
		 				->setSecond(0);	
				
				$date = array('from' => $newDate->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'year':				
				$newDate = new Zend_Date();
				$newDate->setYear($dateStart->getYear())
						->set(1, Zend_Date::DAY_OF_YEAR)
						->setHour(0)
		 				->setMinute(0)
		 				->setSecond(0);	
				
				$date = array('from' => $newDate->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
				
			case 'last_x_hours':
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				$date = array('from' => $now->subHour($shift)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'last_x_days':
				$date = array('from' => $dateStart->subDay($shift-1)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'last_x_weeks':
				$date = array('from' => $dateStart->subWeek($shift)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'last_x_months':
				$date = array('from' => $dateStart->subMonth($shift)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
			case 'last_x_years':
				$date = array('from' => $dateStart->subYear($shift)->toString('YYYY-MM-dd HH:m:s'), 'to' => $dateEnd->toString('YYYY-MM-dd HH:m:s'));
				break;
		}
		
		return $date;
	}
	
	
	protected function isValidSettings($extra)
	{
		if(!isset($extra['customer_action']) || !in_array($extra['customer_action'], array(self::ENABLE_DISCOUNT, self::DISABLE_DISCOUNT)))
			return false;
		
		if(!isset($extra['customer_condition']) || !in_array($extra['customer_condition'], array(self::ALL_TRUE, self::ALL_FALSE, self::ANY_TRUE, self::ANY_FALSE)))
			return false;
		
		if(!isset($extra['customer']) || !count($extra['customer']))
			return false;
		
		return true;
	}
	
	public function isValidCondition($condition, $onSave = false)
	{
		$forSourceValues = $this->getForValuesSource(true, true);
		
		$miscAction = false;
		$miscInterval = false;
		$miscDate = false;
		
		
		if(!isset($condition['criterion']) || !in_array($condition['criterion'], $this->getCriterionValuesSource(true)))
			if($onSave) $miscAction = true;
			else return false;
		
		if(!isset($condition['condition']) || !in_array($condition['condition'], $this->getConditionValuesSource(true)))
			if($onSave) $miscAction = true;
			else return false;
		
		if(isset($condition['for'])) {
			if(!in_array($condition['for'], $this->getForValuesSource(true)))
				if($onSave) $miscAction = true;
				else return false;
			
			if(in_array($condition['for'], $this->getForValuesSource(true, true))) {
				$badInt = false;
				if(!isset($condition['interval'])) $badInt = true;
				else {
					$validator = new Zend_Validate_Digits();
					if(!$validator->isValid($condition['interval'])) $badInt = true;
						
					if($condition['interval'] == '0')
						if($onSave) $miscInterval = true;
					else return false;
				}
				if($badInt)
					if($onSave) $miscAction = true;
				else return false;
			}
		}		
		
		if(isset($condition['qa'])) {
			$validator = new Zend_Validate_Digits();
			if(!$validator->isValid($condition['qa']))
				if($onSave) $miscAction = true;
				else return false;
		}
		
		if(isset($condition['date']) && !in_array($condition['condition'], $this->getConditionValuesSource(false, true))) {
		    if (version_compare(Zend_Version::VERSION, '1.10.0') >= 0) {		       
		        $validator = new Zend_Validate_Date(array('format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)));
		    } else {
		        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);		        
		        $validator = new Zend_Validate_Date('M/d/yy');
		    }
			if(!$validator->isValid($condition['date']))
				if($onSave) $miscDate = true;
				else return false;
		}
		
		if(isset($condition['products'])) {
			$validator = new Zend_Validate_NotEmpty();
			if(!$validator->isValid($condition['products']))
				if($onSave) $miscAction = true;
				else return false;
		}
		
		if(isset($condition['category'])) {
		    $validator = new Zend_Validate_NotEmpty();
		    if(!$validator->isValid($condition['category']))
		    if($onSave) $miscAction = true;
		    else return false;
		}
		
		if(isset($condition['customer'])) {
			$validator = new Zend_Validate_NotEmpty();
			if(!$validator->isValid($condition['customer']))
				if($onSave) $miscAction = true;
				else return false;
		}
		
		if(isset($condition['data'])) {
			$validator = new Zend_Validate_NotEmpty();
			$inputTmpl = array(Mage::helper('dexconditions')->__('first name'), Mage::helper('dexconditions')->__('last name'));
			if(!$validator->isValid($condition['data']) || in_array($condition['data'], $inputTmpl))
				if($onSave) $miscAction = true;
				else return false;
		}
		
		if(!$onSave) return true;
		else {
			$errors = array();
			
			if($miscAction || $miscInterval || $miscDate) $errors[] = Mage::helper('dexconditions')->__("Please check customer conditions on 'Customer Specific Conditions' tab.");
			if($miscAction) $errors[] = Mage::helper('dexconditions')->__("Some of your customer conditions have important data missing. Please fill all the necessary fields.");
			if($miscInterval) $errors[] = Mage::helper('dexconditions')->__("Intervals in customer conditions can not be equal to zero.");
			if($miscDate) $errors[] = Mage::helper('dexconditions')->__("There are invalid dates in the customer conditions. Please use the datepicker to select a date.");
			
			return $errors;
		}
	}
	
	public function getConditionValuesSource($validation = false, $special = false)
	{
		if($special)
			return array('yesterday', 'today', 'is_na', 'is_not_na', 'tomorrow', 'this_year', 'previous_year', 'this_week', 'this_month', 'previous_month', 'next_week', 'previous_week', 'next_month');
		
		if($validation)
			return array('is_one', 'is_not_one', 'is_na', 'is_not_na', 'reviewed', 'not_reviewed', 'purchased', 'not_purchased', 'purchased_int', 'not_purchased_int',
					'yesterday', 'today', 'tomorrow', 'this_year', 'previous_year', 'this_week', 'previous_week', 'this_month', 'previous_month', 'next_week', 'next_month', '==', '!=', '>=', '<=', '>=d', '<=d', '>', '<', 'sub', 'notsub');
		
		return 
				array(
    			'is_na' => array('label' => Mage::helper('dexconditions')->__('is specified'), 'value' => 'is_na'),
    			'is_not_na' => array('label' => Mage::helper('dexconditions')->__('is not specified'), 'value' => 'is_not_na'),
    			'is_one' => array('label' => Mage::helper('dexconditions')->__('is one of the following'), 'value' => 'is_one'),
    			'is_not_one' => array('label' => Mage::helper('dexconditions')->__('is not one of the following'), 'value' => 'is_not_one'),
    			'purchased' => array('label' => Mage::helper('dexconditions')->__('ordered before'), 'value' => 'purchased'),
    			'not_purchased' => array('label' => Mage::helper('dexconditions')->__('not ordered before'), 'value' => 'not_purchased'),
    			'reviewed' => array('label' => Mage::helper('dexconditions')->__('have been reviewed by the customer'), 'value' => 'reviewed'),
    			'not_reviewed' => array('label' => Mage::helper('dexconditions')->__('have not been reviewed by the customer'), 'value' => 'not_reviewed'),
    			'purchased_int' => array('label' => Mage::helper('dexconditions')->__('ordered'), 'value' => 'purchased_int'),
    			'not_purchased_int' => array('label' => Mage::helper('dexconditions')->__('not ordered'), 'value' => 'not_purchased_int'),
    			'yesterday' => array('label' => Mage::helper('dexconditions')->__('was yesterday'), 'value' => 'yesterday'),
    			'today' => array('label' => Mage::helper('dexconditions')->__('is today'), 'value' => 'today'),
    			'tomorrow' => array('label' => Mage::helper('dexconditions')->__('is tomorrow'), 'value' => 'tomorrow'),
    			'this_week' => array('label' => Mage::helper('dexconditions')->__('is this week'), 'value' => 'this_week'),
    			'previous_week' => array('label' => Mage::helper('dexconditions')->__('was last week'), 'value' => 'previous_week'),
    			'next_week' => array('label' => Mage::helper('dexconditions')->__('is next week'), 'value' => 'next_week'),
    			'this_month' => array('label' => Mage::helper('dexconditions')->__('is this month'), 'value' => 'this_month'),
    			'previous_month' => array('label' => Mage::helper('dexconditions')->__('was last month'), 'value' => 'previous_month'),
    			'next_month' => array('label' => Mage::helper('dexconditions')->__('is next month'), 'value' => 'next_month'),
    			'this_year' => array('label' => Mage::helper('dexconditions')->__('is this year'), 'value' => 'this_year'),
    			'previous_year' => array('label' => Mage::helper('dexconditions')->__('was last year'), 'value' => 'previous_year'),
    			'==' => array('label' => Mage::helper('dexconditions')->__('is'), 'value' => '=='),
    			'!=' => array('label' => Mage::helper('dexconditions')->__('is not'), 'value' => '!='),
    			'>=' => array('label' => Mage::helper('dexconditions')->__('is equal to or greater than'), 'value' => '>='),
    			'<=' => array('label' => Mage::helper('dexconditions')->__('is equal to or less than'), 'value' => '<='),
				'>=d' => array('label' => Mage::helper('dexconditions')->__('is equal to or later than'), 'value' => '>=d'),
				'<=d' => array('label' => Mage::helper('dexconditions')->__('is equal to or before'), 'value' => '<=d'),
    			'>' => array('label' => Mage::helper('dexconditions')->__('is greater than'), 'value' => '>'),
    			'<' => array('label' => Mage::helper('dexconditions')->__('is less than'), 'value' => '<'),
				'sub' => array('label' => Mage::helper('dexconditions')->__('is subscribed to newsletter'), 'value' => 'sub'),
				'notsub' => array('label' => Mage::helper('dexconditions')->__('is not subscribed to newsletter'), 'value' => 'notsub')
    	);
	}
	
	public function getCustomerPastIntValues()
	{
		return array(
				'last_x_hours' => Mage::helper('dexconditions')->__('hour(s)'),
				'last_x_days' => Mage::helper('dexconditions')->__('day(s)'),
				'last_x_weeks' => Mage::helper('dexconditions')->__('week(s)'),
				'last_x_months' => Mage::helper('dexconditions')->__('month(s)'),
				'last_x_years' => Mage::helper('dexconditions')->__('year(s)'));
	}
	
	public function getForValuesSource($validation = false, $special = false)
	{
		if($validation) {
			
			if($special) return array('last_x_hours', 'last_x_days', 'last_x_weeks', 'last_x_months', 'last_x_years');
			return array('hour', 'previous_hour', 'today', 'yesterday', 'week', 'month', 'year', 'last_x_hours', 'last_x_days', 'last_x_weeks', 'last_x_months', 'last_x_years');
		}
		
		return
				array(
						'hour' => array('label' => Mage::helper('dexconditions')->__('during the current hour'), 'value' => 'hour'),
						'previous_hour' => array('label' => Mage::helper('dexconditions')->__('in the prevous hour'), 'value' => 'previous_hour'),
						'today' => array('label' => Mage::helper('dexconditions')->__('today'), 'value' => 'today'),
						'yesterday' => array('label' => Mage::helper('dexconditions')->__('yesterday'), 'value' => 'yesterday'),
						'week' => array('label' => Mage::helper('dexconditions')->__('during this week'), 'value' => 'week'),
						'month' => array('label' => Mage::helper('dexconditions')->__('during this month'), 'value' => 'month'),
						'year' => array('label' => Mage::helper('dexconditions')->__('during this year'), 'value' => 'year'),
						'last_x_hours' => array('label' => Mage::helper('dexconditions')->__('during the last X hours'), 'value' => 'last_x_hours'),
						'last_x_days' => array('label' => Mage::helper('dexconditions')->__('during the last X days'), 'value' => 'last_x_days'),
						'last_x_weeks' => array('label' => Mage::helper('dexconditions')->__('during the last X weeks'), 'value' => 'last_x_weeks'),
						'last_x_months' => array('label' => Mage::helper('dexconditions')->__('during the last X months'), 'value' => 'last_x_months'),
						'last_x_years' => array('label' => Mage::helper('dexconditions')->__('during the last X years'), 'value' => 'last_x_years'),
				);
	}
		
	public function getSexSelectValues($validation = false)
	{
		if($validation)
			return array('2', '1');
		
		return array(
    			
    			array('label' => Mage::helper('dexconditions')->__('male'), 'value' => 1),
    			array('label' => Mage::helper('dexconditions')->__('female'), 'value' => 2),
				array('label' => Mage::helper('dexconditions')->__('undefined'), 'value' => 0));
	}
	
	public function getGroups($validation = false)
	{
		$customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
		
	
		foreach ($customerGroups as $key => $group) {
			
			if ($group['value'] == 0) 
				unset($customerGroups[$key]);
		}
		
		
		if($validation) {
			$groups = array();
			foreach ($customerGroups as $group)
				$groups[] = $group['value'];
			return $groups;
		}
			
	
		return $customerGroups;
	}
	
	protected function getSalesConditions($subject)
	{
		$salesConditions = array(					
					'total_income_amount',
					'total_revenue_amount',
					'total_profit_amount',
					'total_invoiced_amount',
					'total_paid_amount',
					'total_refunded_amount',
					'total_tax_amount',
					'total_tax_amount_actual',
					'total_shipping_amount',
					'total_shipping_amount_actual',
					'total_discount_amount',
					'total_discount_amount_actual',
					'total_canceled_amount',
					'orders_count',
					'total_qty_ordered',
					'total_qty_invoiced',
					'total_income_amount_int',
					'total_revenue_amount_int',
					'total_profit_amount_int',
					'total_invoiced_amount_int',
					'total_paid_amount_int',
					'total_refunded_amount_int',
					'total_tax_amount_int',
					'total_tax_amount_actual_int',
					'total_shipping_amount_int',
					'total_shipping_amount_actual_int',
					'total_discount_amount_int',
					'total_discount_amount_actual_int',
					'total_canceled_amount_int',
					'orders_count_int',
					'total_qty_ordered_int',
					'total_qty_invoiced_int');
		
		return in_array($subject, $salesConditions);
	}
	
	public function getCriterionValuesSource($validation = false, $hash = false)
	{
		$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
		
		
		
		if($hash) 
			return array(
					'number' => array(
							array('value' => 'next_order_number'), 
							array('value' => 'review_count')),
					'quantity' => array(
							array('value' => 'orders_count'),	
							array('value' => 'total_qty_ordered'),	
							array('value' => 'total_qty_invoiced'),	
							array('value' => 'orders_count_int'),	
							array('value' => 'total_qty_ordered_int'),	
							array('value' => 'total_qty_invoiced_int')),
					'amount' => array(
							array('value' => 'orders_av_amount'), 
							array('value' => 'total_income_amount'), 
							array('value' => 'total_revenue_amount'), 
							array('value' => 'total_profit_amount'), 
							array('value' => 'total_invoiced_amount'), 
							array('value' => 'total_paid_amount'), 
							array('value' => 'total_refunded_amount'), 
							array('value' => 'total_tax_amount'), 
							array('value' => 'total_tax_amount_actual'), 
							array('value' => 'total_shipping_amount'), 
							array('value' => 'total_shipping_amount_actual'), 
							array('value' => 'total_discount_amount'), 
							array('value' => 'total_discount_amount_actual'), 
							array('value' => 'total_canceled_amount'), 
							array('value' => 'orders_av_amount_int'),
							array('value' => 'total_income_amount_int'), 
							array('value' => 'total_revenue_amount_int'), 
							array('value' => 'total_profit_amount_int'), 
							array('value' => 'total_invoiced_amount_int'), 
							array('value' => 'total_paid_amount_int'), 
							array('value' => 'total_refunded_amount_int'), 
							array('value' => 'total_tax_amount_int'), 
							array('value' => 'total_tax_amount_actual_int'), 
							array('value' => 'total_shipping_amount_int'), 
							array('value' => 'total_shipping_amount_actual_int'), 
							array('value' => 'total_discount_amount_int'), 
							array('value' => 'total_discount_amount_actual_int'), 
							array('value' => 'total_canceled_amount_int'),
							array('value' => 'specific_cat'),
							array('value' => 'specific_cat_int')),
					'first_name' => array(
							array('value' => 'customer_first_name')),
					'last_name' => array(
							array('value' => 'customer_last_name'))
					);
		
		if($validation)
			return array(
					'customer_specific', 
					'orders_av_amount', 
					'total_income_amount',
					'total_revenue_amount',
					'total_profit_amount',
					'total_invoiced_amount',
					'total_paid_amount',
					'total_refunded_amount',
					'total_tax_amount',
					'total_tax_amount_actual',
					'total_shipping_amount',
					'total_shipping_amount_actual',
					'total_discount_amount',
					'total_discount_amount_actual',
					'total_canceled_amount',
					'orders_count',
					'total_qty_ordered',
					'total_qty_invoiced',
					'next_order_number',
					'specific_product_all',
					'specific_product_any',
					'orders_av_amount_int',
					'total_income_amount_int',
					'total_revenue_amount_int',
					'total_profit_amount_int',
					'total_invoiced_amount_int',
					'total_paid_amount_int',
					'total_refunded_amount_int',
					'total_tax_amount_int',
					'total_tax_amount_actual_int',
					'total_shipping_amount_int',
					'total_shipping_amount_actual_int',
					'total_discount_amount_int',
					'total_discount_amount_actual_int',
					'total_canceled_amount_int',
					'orders_count_int',
					'total_qty_ordered_int',
					'total_qty_invoiced_int',
					'specific_product_int_all',
					'specific_product_int_any',
					'specific_cat',
					'specific_cat_int',
					'review_count',
					'review_last_date',
					'review_products',
					'customer_first_name',
					'customer_last_name',
					'customer_sex',
					'customer_group',
					'customer_newsletter',
					'customer_birthday',
					'customer_account_date',
			        'cart_items_past_orders_any',
					'cart_items_past_orders_all',
			        'cart_items_past_orders_int_any',
					'cart_items_past_orders_int_all'
					);
		
		return 
				array(
    					array('label' => Mage::helper('dexconditions')->__('-- Please select --'), 'value' => ''),
    					array('label' => Mage::helper('dexconditions')->__('Specific Customers'), 'value' => array(
    							array('label' => Mage::helper('dexconditions')->__("If customer ID"), 'value' => 'customer_specific')
    					)),				    
    
    					array('label' => Mage::helper('dexconditions')->__('Customer Orders'), 'value' => array()),
    					array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Lifetime'), 'value' => array()),
    					
						array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Amount'), 'value' => array(
    						
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If order average amount'), 'value' => 'orders_av_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total income amount'), 'value' => 'total_income_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total revenue amount'), 'value' => 'total_revenue_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total profit amount'), 'value' => 'total_profit_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total invoiced amount'), 'value' => 'total_invoiced_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total paid amount'), 'value' => 'total_paid_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total refunded amount'), 'value' => 'total_refunded_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total tax amount'), 'value' => 'total_tax_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total tax amount'), 'value' => 'total_tax_amount_actual'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total shipping amount'), 'value' => 'total_shipping_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total shipping amount'), 'value' => 'total_shipping_amount_actual'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total discount amount'), 'value' => 'total_discount_amount'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total discount amount'), 'value' => 'total_discount_amount_actual'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total canceled amount'), 'value' => 'total_canceled_amount')
    							   
    					)),
						
    					array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Quantity'), 'value' => array(
   
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If order quantity'), 'value' => 'orders_count'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total quantity of items ordered'), 'value' => 'total_qty_ordered'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total quantity of items invoiced'), 'value' => 'total_qty_invoiced')
    					)), 					

						array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Advanced'), 'value' => array(
								
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If the next order number'), 'value' => 'next_order_number'),
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If all products with the following IDs were'), 'value' => 'specific_product_all'),
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If any products with the following IDs were'), 'value' => 'specific_product_any'),
						        array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If the amount for the ordered products in category'), 'value' => 'specific_cat'),
						)),
				    
    				    array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Cart Items'), 'value' => array(
    				        array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__("If at least one of the specified products is in the cart and if it was"), 'value' => 'cart_items_past_orders_any'),
    				        array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__("If all specified products are in the cart and they were"), 'value' => 'cart_items_past_orders_all')
    				    )),
						
    					array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('During specific period'), 'value' => array()),
						
    					array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Amount'), 'value' => array(
    						
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If order average amount'), 'value' => 'orders_av_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total income amount'), 'value' => 'total_income_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total revenue amount'), 'value' => 'total_revenue_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total profit amount'), 'value' => 'total_profit_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total invoiced amount'), 'value' => 'total_invoiced_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total paid amount'), 'value' => 'total_paid_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total refunded amount'), 'value' => 'total_refunded_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total tax amount'), 'value' => 'total_tax_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total tax amount'), 'value' => 'total_tax_amount_actual_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total shipping amount'), 'value' => 'total_shipping_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total shipping amount'), 'value' => 'total_shipping_amount_actual_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total discount amount'), 'value' => 'total_discount_amount_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If actual total discount amount'), 'value' => 'total_discount_amount_actual_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total canceled amount'), 'value' => 'total_canceled_amount_int')
    							   
    					)),
						
    					array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Quantity'), 'value' => array(
   
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If order quantity'), 'value' => 'orders_count_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total quantity of items ordered'), 'value' => 'total_qty_ordered_int'),
    							array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If total quantity of items invoiced'), 'value' => 'total_qty_invoiced_int')
    					)), 					

						array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Advanced'), 'value' => array(
	
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If all products with the following IDs were'), 'value' => 'specific_product_int_all'),
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If any products with the following IDs were'), 'value' => 'specific_product_int_any'),
								array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('If the amount for the ordered products in category'), 'value' => 'specific_cat_int')
						)),
				    
    				    array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__('Cart Items'), 'value' => array(
    				        array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__("If at least one of the specified products is in the cart and if it was"), 'value' => 'cart_items_past_orders_int_any'),
    				        array('label' => str_repeat($nonEscapableNbspChar, 6) . Mage::helper('dexconditions')->__("If all specified products are in the cart and they were"), 'value' => 'cart_items_past_orders_int_all')
    				    )),
						
    					array('label' => Mage::helper('dexconditions')->__('Customer Reviews'), 'value' => array(
    
    							array('label' => Mage::helper('dexconditions')->__('If review count'), 'value' => 'review_count'),
    							array('label' => Mage::helper('dexconditions')->__('If latest review date'), 'value' => 'review_last_date'),
    							array('label' => Mage::helper('dexconditions')->__('If products with the following IDs'), 'value' => 'review_products')
    					)),
						
    					array('label' => Mage::helper('dexconditions')->__('Personal Information'), 'value' => array(
    
    							array('label' => Mage::helper('dexconditions')->__("If first name"), 'value' => 'customer_first_name'),
    							array('label' => Mage::helper('dexconditions')->__("If last name"), 'value' => 'customer_last_name'),
    							array('label' => Mage::helper('dexconditions')->__("If sex"), 'value' => 'customer_sex'),
    							array('label' => Mage::helper('dexconditions')->__("If birthday"), 'value' => 'customer_birthday')
    					)),
						
    					array('label' => Mage::helper('dexconditions')->__('Account Information'), 'value' => array(
    							array('label' => Mage::helper('dexconditions')->__("If account registration date"), 'value' => 'customer_account_date'),
    							array('label' => Mage::helper('dexconditions')->__("If customer group"), 'value' => 'customer_group'),
    							array('label' => Mage::helper('dexconditions')->__("If customer is (newsletter)"), 'value' => 'customer_newsletter')
    					))
						
						);
	}
	
	public function getStoreSwitcherValues()
	{
		return Mage::helper('dexconditions')->getStoreSwitcherValues();
	}

	
	public function getOrderItemStatuses()
	{
		$statuses = Mage_Sales_Model_Order_Item::getStatuses();
		$values = array();
		foreach ($statuses as $code => $label) {
				if($code == Mage_Sales_Model_Order_Item::STATUS_RETURNED) continue;
				$values[] = array(
						'label' => Mage::helper('reports')->__($label),
						'value' => $code
				);
			
		}
		return $values;
	}
	
	public function getOrderStatusValues()
	{
		return array(
				array('label' => Mage::helper('dexconditions')->__('with any order status'), 'value' => 'any'),
				array('label' => Mage::helper('dexconditions')->__('with the following order statuses:'), 'value' => 'specified'));
	}
	
	public function getStoreCritValues($all = false)
	{
		return array(
				array('label' => ($all) ? Mage::helper('dexconditions')->__('on all websites') : Mage::helper('dexconditions')->__('on any website'), 'value' => 'any'),
				array('label' => Mage::helper('dexconditions')->__('in the following stores:'), 'value' => 'specified'));
	}
	
	public function getOrderStatuses()
	{
		$statuses = Mage::getModel('sales/order_config')->getStatuses();
		$values = array();
		foreach ($statuses as $code => $label) {
			if (false === strpos($code, 'pending')) {
				$values[] = array(
						'label' => Mage::helper('reports')->__($label),
						'value' => $code
				);
			}
		}
		return $values;
	}
	
	public function getOrderItemStatusValues()
	{
		return array(
				array('label' => Mage::helper('dexconditions')->__('with any order item status'), 'value' => 'any'),
				array('label' => Mage::helper('dexconditions')->__('with the following order item statuses:'), 'value' => 'specified'));
	}
}