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

class AddInMage_DexConditions_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENABLED  = 'addinmage_dexconditions/general/use';	
	protected $_auth_msg_displayed = false;
	
	public function useAdvancedConditions()
	{		
		return Mage::getStoreConfig(self::XML_PATH_ENABLED, Mage::app()->getStore());
	}
	
	public function addFormElements($form, $extra_data)
	{
		$dexWeekIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Week');
		$dexDayIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Day');
		$dexSalesIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Sales');
		$dexCustomerIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Customer');
		
		if($dexDayIncl) Mage::getBlockSingleton('AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Form_Day')->addFormData($form, $extra_data);
		if($dexWeekIncl) Mage::getBlockSingleton('AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Form_Week')->addFormData($form);
		if($dexSalesIncl) Mage::getBlockSingleton('AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Form_Sales')->addFormData($form, $extra_data);
		if($dexCustomerIncl) Mage::getBlockSingleton('AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Form_Customer')->addFormData($form, $extra_data);
			
	}
	
	public function addHeadItems($head)
	{
		
		$dexCustomerIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Customer');
		if($dexCustomerIncl) {
			$head->addItem('js', 'prototype/window.js')
			->addItem('js', 'mage/adminhtml/wysiwyg/widget.js')
			->addItem('js', 'addinmage/dexconditions/selector.js')
			->addItem('js_css', 'prototype/windows/themes/default.css');
			
			if(file_exists(Mage::getDesign()->getSkinBaseDir(array('_area' => 'adminhtml')) . DS . 'lib/prototype/windows/themes/magento.css'))
				$head->addCss('lib/prototype/windows/themes/magento.css');
			else $head->addItem('js_css', 'prototype/windows/themes/magento.css');
		}						
	}
	
	public function getDexSfEdition()
	{
		return Mage::helper('dexconditions/setup')->getDexEdition();
	}
	
	public function getNinIds($tmp_rules) 
	{
		$nin = array();
		
		$dexWeekIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Week');
		$dexDayIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Day');
		$dexSalesIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Sales');
		$dexCustomerIncl = @class_exists('AddInMage_DexConditions_Helper_Types_Customer');
		
		foreach ($tmp_rules as $rule) {
			
			if($rule->getData('extra_conditions')) {
				
				$extra = unserialize($rule->getData('extra_conditions'));
				$ruleId = $rule->getData('rule_id');
				
				// Weekly
				if($dexWeekIncl) {
					$dexWeekHelper = Mage::helper('dexconditions/types_week');
					$dexWeekNinId = $dexWeekHelper->getConditionNinId($extra, $ruleId);
					if($dexWeekNinId) $nin[] = $dexWeekNinId;
				}
				

				// Time Frames
				if($dexDayIncl) {
					$dexDayHelper = Mage::helper('dexconditions/types_day');
					$dexDayNinId = $dexDayHelper->getConditionNinId($extra, $ruleId);
					if($dexDayNinId) $nin[] = $dexDayNinId;
				}
				
				// Sales
				if($dexSalesIncl) {
					$dexSalesHelper = Mage::helper('dexconditions/types_sales');
					$dexSalesNinId = $dexSalesHelper->getConditionNinId($extra, $ruleId);
					if($dexSalesNinId) $nin[] = $dexSalesNinId;
				}
				
			    // Customers
				if($dexCustomerIncl) {
					$dexCustomerHelper = Mage::helper('dexconditions/types_customer');
					$dexCustomerNinId = $dexCustomerHelper->getConditionNinId($extra, $ruleId);
					if($dexCustomerNinId == 'not_logged_in' || $dexCustomerNinId == 'bad_rule') $nin[] = $ruleId;
					if($dexCustomerNinId) $nin[] = $dexCustomerNinId;

				}
			}
		}

		
		return array_unique($nin);
	}
	
	public function decLog($method, $e)
	{
	
		try {
				
			$io = new Varien_Io_File();
			$io->checkAndCreateFolder(Mage::getBaseDir('log') .DS. 'addinmage');
	
		} catch (Exception $e) {
				
			Mage::log($e);
	
		}
	
		$file = 'addinmage/dec.log';
		$environment = "\n";
		$environment .= 'PHP: ' . phpversion();
		$environment .= "\n";
		$environment .= 'MAGENTO: ' . Mage::getVersion();
		$environment .= "\n";
		$environment .= 'TRACE: ' . $method;
		$environment .= "\n";
		$environment .= $e->__toString();
		$environment .= "\n \n";
		Mage::log($environment, null, $file);
	
	
	}
	
	public function getProductTypeOptions()
	{
		$options = array(array('value' => '', 'label' => $this->__('Please choose the product type...')));
		$types = Mage::getModel('catalog/product_type')->getOptionArray();
		
		foreach ($types as $type => $name) {
			$options[] = array('label' => $name, 'value' => $type);
		}
		
		return $options;
	}
	
	public function aasort (&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        
        $array = $ret;
    }
	
	public function getProductConfigurableOptions()
	{
	    $options = array(array('value' => '', 'label' => $this->__('Please choose a condition to add...')));
	    
	    $superAttributes = Mage::getResourceModel('catalog/product_type_configurable_attribute_collection');

	    if($superAttributes->getSize()) {
	        
	        $superAttributesIds = array();
	        
	        foreach ($superAttributes->getData() as $attribute)
	            $superAttributesIds[] = $attribute['attribute_id'];
	        
	        $superAttributesIds = array_unique($superAttributesIds);
	        
	        $attributeCollection = Mage::getResourceModel('eav/entity_attribute_collection')->addFieldToFilter('attribute_id', array('in' => $superAttributesIds));
	        
	        
	        
	        foreach ($attributeCollection as $attribute) {
	            
	            $type = $attribute->getFrontendInput();
	            
	            $optionsTmp[$attribute->getFrontend()->getLabeL()] = array();
	            
	            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setAttributeFilter($attribute->getId())->setStoreFilter(0, false);
	            
	            if($valuesCollection->getSize()) {
	            
    	            $attributeOptions = $valuesCollection->getData();
    	            $this->aasort($attributeOptions, 'value');
    	            
    	            $optionsTmp = array();  
    	            $prefix = $attribute->getFrontend()->getLabeL() . ': ';  	            
    	           
    	            
    	            foreach ($attributeOptions as $option)
    	                $optionsTmp[] = array('label' => $prefix . $option['value'], 'value' => $attribute->getId().'+'.$option['option_id']); 	            
    	            
    	            if(count($optionsTmp))
    	                $options[] = array('label' => $attribute->getFrontend()->getLabeL(), 'value' => $optionsTmp);   	                
	            }
	        }
	    }
	    
	    return $options;
	}
	
	public function getProductCustomOptions()
	{
		
		$store = Mage::app()->getStore()->getId();
		$col = Mage::getModel('catalog/product_option')->getCollection()->getOptions($store);
		
		$options = array(array('value' => '', 'label' => $this->__('Please choose a condition to add...')));
		$data = array();
		$data_tmp = array();
	
		foreach ($col as $option) {
		
			if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
				foreach ($option->getValuesCollection() as $op) {
					$data_tmp[$option->getTitle()]['sub'][$op->getTitle()][$op->getSku()][$option->getType()] = array('title' => $op->getTitle(), 'sku' => $op->getSku(), 'type' => $option->getType());
				}
			} 
			else {
				$data_tmp[$option->getTitle()]['sub'][$option->getTitle()][$option->getSku()][$option->getType()] = array('title' => $option->getTitle(), 'sku' => $option->getSku(), 'type' => $option->getType());
			}
			 
		}
		
		foreach ($data_tmp as $key => $tmp){
			if(isset($tmp['sub'])) {
				foreach ($tmp['sub'] as $sub_array) {
					foreach ($sub_array as $tmp_array) {
						foreach ($tmp_array as $tmp_data_array) {
							$data[$key][] = array('title' => $tmp_data_array['title'], 'sku' => $tmp_data_array['sku'], 'type' => $tmp_data_array['type']);
						}
					}
				}
			} 	
		}
		
		
		foreach ($data as $el => $values) {
			
			$tmp_options = array();
			foreach ($values as $value) {
				$tmp_options[] = array('label' => $value['title'].' - '.$this->__('[sku] - %s',$value['sku']), 'value' => $value['type'].'+=+'.$value['sku']); 
			}
			$options[] = array('label' => $el, 'value' => $tmp_options);

		}
	
		return $options;
	}
	
	public function canShowStoreSwitcher()
	{
		return !Mage::app()->isSingleStoreMode();
	}
	
	public function getStoreSwitcherValues()
	{
		$switcherBlock = Mage::getBlockSingleton('adminhtml/store_switcher');
		$websites = $switcherBlock->getWebsiteCollection();
		$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
	
		$options = array();
	
		foreach ($websites as $_website) {
			$showWebsite = false;
			foreach ($switcherBlock->getGroupCollection($_website) as $_group) {
	
				$showGroup = false;
				foreach ($switcherBlock->getStoreCollection($_group) as $_store) {
					if ($showWebsite == false) {
						$showWebsite = true;
						$options[] = array('label' => $_website->getName(), 'value' => implode(',', $_website->getStoreIds()));
					}
					if ($showGroup == false) {
						$showGroup = true;
						$options[] = array('label' => str_repeat($nonEscapableNbspChar, 3) . $_group->getName(), 'value' => implode(',', $_group->getStoreIds()));
					}
					$options[] = array('label' => str_repeat($nonEscapableNbspChar, 6) . $_store->getName(), 'value' => $_store->getId());
				}
			}
		}
		return $options;
	
	}
	
	public function modelRefactored()
	{

		if (version_compare(Mage::getVersion(), '1.7.0.2') >= 0) return true;
		return false;

	}
}