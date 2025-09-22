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

abstract class AddInMage_DexConditions_Model_Rule_Condition_Product_Abstract extends AddInMage_DexConditions_Model_Toggler
{
     /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();     
        
	    if('dec_custom_options' == $attrCode) {
	    	$product = $object->getDecPco();
	    	
	    	$product_data = $product->getCustomOption('info_buyRequest');
	    	$options_arr = unserialize($product_data->getValue());
	    	
	    	$options = (isset($options_arr['options'])) ?
// 	    	Mage::getModel('catalog/product_option')->getCollection()->addIdsToFilter(array_keys($options_arr['options'])) : array();
	    	Mage::getModel('catalog/product_option')->getCollection()->addFieldToFilter('main_table.option_id', array_keys($options_arr['options'])) : array();
	    	
	    		
	    	$data = array();
	    	foreach ($options as $option) {
	    		if($option->getSku())
	    			$data[] = array('sku' => $option->getSku(), 'type' => $option->getType(), 'option_id' => $option->getOptionId(), 'value' => $options_arr['options'][$option->getOptionId()]);
	    		else {
	    			if(is_array($options_arr['options'][$option->getOptionId()])) {
	    				$col = Mage::getModel('catalog/product_option_value')->getValuesByOption(array_values($options_arr['options'][$option->getOptionId()]), $option->getOptionId(), Mage::app()->getStore()->getId());
	    				foreach ($col as $value)
	    					$data[] = array('sku' => $value->getSku(), 'type' => $option->getType(), 'option_id' => $value->getOptionId());
	    			}
	    				
	    			else {
	    				$col = Mage::getModel('catalog/product_option_value')->getValuesByOption(array($options_arr['options'][$option->getOptionId()]), $option->getOptionId(), Mage::app()->getStore()->getId());
	    				foreach ($col as $value)
	    					$data[] = array('sku' => $value->getSku(), 'type' => $option->getType(), 'option_id' => $value->getOptionId());
	    			}
	    		}
	    	}
	    	
	    	return (Mage::helper('dexconditions')->useAdvancedConditions()) ? $this->validateDecPco($data) : true;
	   	}
	   	if('dec_configurable_options' == $attrCode) {	
	   	    $product = $object->getDecPconfop();
	   	    
	   	    $product_data = $product->getCustomOption('info_buyRequest');
	   	    $options_arr = unserialize($product_data->getValue());  
	   	    
	   	    $options = (isset($options_arr['super_attribute'])) ? $options_arr['super_attribute'] : array();
	   	    
	   		return (Mage::helper('dexconditions')->useAdvancedConditions()) ? $this->validateDecPconfop($options) : true;
	   	}
	   	if('dec_product_type' == $attrCode) {	
	   		return (Mage::helper('dexconditions')->useAdvancedConditions()) ? $this->validateExtraSimple($object->getDecPtype()) : true;
	   	}
	   	if('dec_product_review' == $attrCode) {
	   		$product_id = $object->getDecProductId();
	   		$store = $object->getStore()->getId();
	   		$summaryData = Mage::getModel('review/review_summary')->setStoreId($store)->load($product_id);
	   		$reviews_count = (!$summaryData->getReviewsCount()) ? 0 : $summaryData->getReviewsCount();
	   		return (Mage::helper('dexconditions')->useAdvancedConditions()) ? $this->validateExtraSimple($reviews_count) : true;
	   	}
	   	if('dec_product_stock_inventory' == $attrCode) {
	   		$product = $object->getDecPco();
	   		$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
	   		$qtyStock = ($qtyStock) ? (int) $qtyStock : 0;
	   		return (Mage::helper('dexconditions')->useAdvancedConditions()) ? $this->validateExtraSimple($qtyStock) : true;
	   	}
        
        if ('category_ids' == $attrCode) {
        	
            return $this->validateAttribute($object->getAvailableInCategories());
        } elseif (! isset($this->_entityAttributeValues[$object->getId()])) {
        	
            if (!$object->getResource()) {
            	
                return false;
            }
            $attr = $object->getResource()->getAttribute($attrCode);
           
            if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                $this->setValue(strtotime($this->getValue()));
                $value = strtotime($object->getData($attrCode));
                
                return $this->validateAttribute($value);
            }

            if ($attr && $attr->getFrontendInput() == 'multiselect') {
            	
                $value = $object->getData($attrCode);
                $value = strlen($value) ? explode(',', $value) : array();
                return $this->validateAttribute($value);
            }
           
            return parent::validate($object);
        } else {
            $result = false; // any valid value will set it to TRUE
           
            // remember old attribute state
            $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;

            foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                $attr = $object->getResource()->getAttribute($attrCode);
                if ($attr && $attr->getBackendType() == 'datetime') {
                    $value = strtotime($value);
                } else if ($attr && $attr->getFrontendInput() == 'multiselect') {
                    $value = strlen($value) ? explode(',', $value) : array();
                }

                $object->setData($attrCode, $value);
                $result |= parent::validate($object);

                if ($result) {
                    break;
                }
            }
            
            if (is_null($oldAttrValue)) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }
            
            return (bool) $result;
        }
    }
}
