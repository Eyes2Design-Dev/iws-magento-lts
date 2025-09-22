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

class AddInMage_DexConditions_Model_Extra_Conditions extends AddInMage_DexConditions_Model_Rule_Condition_Product_Abstract
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {        
    	parent::_addSpecialAttributes($attributes);
    	
    	$attributes['quote_item_qty'] = Mage::helper('salesrule')->__('Quantity in cart');
    	$attributes['quote_item_price'] = Mage::helper('salesrule')->__('Price in cart');
    	$attributes['quote_item_row_total'] = Mage::helper('salesrule')->__('Row total in cart');
    
    	
        $attributes['dec_custom_options'] = Mage::helper('dexconditions')->__('Product Custom Option SKU');
        $attributes['dec_configurable_options'] = Mage::helper('dexconditions')->__('Product Configurable Option');
        $attributes['dec_product_type'] = Mage::helper('dexconditions')->__('Product Type');
        $attributes['dec_product_stock_inventory'] = Mage::helper('dexconditions')->__('Product Stock Quantity');
        $attributes['dec_product_review'] = Mage::helper('dexconditions')->__('Product Review Count');
     	//return parent::_addSpecialAttributes($attributes);
    }
    
    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
         
    
        if (!$this->getData('value_select_options')) {
            if($this->getAttribute() === 'dec_custom_options'){
                $this->setData('value_select_options', Mage::helper('dexconditions')->getProductCustomOptions());
            } elseif($this->getAttribute() === 'dec_configurable_options'){
                $this->setData('value_select_options', Mage::helper('dexconditions')->getProductConfigurableOptions());
            } elseif($this->getAttribute() === 'dec_product_type'){
                $this->setData('value_select_options', Mage::helper('dexconditions')->getProductTypeOptions());
            } elseif($this->getAttribute()==='attribute_set_id') {
                $entityTypeId = Mage::getSingleton('eav/config')
                ->getEntityType('catalog_product')->getId();
                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId)
                ->load()->toOptionArray();
                $this->setData('value_select_options', $options);
            } elseif (is_object($this->getAttributeObject()) && $this->getAttributeObject()->usesSource()) {
                if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                $this->setData('value_select_options', $optionsArr);
            }
        }
        return $this->getData('value_select_options');
    }
    
    /**
     * Prepares values options to be used as select options or hashed array
     * Result is stored in following keys:
     *  'value_select_options' - normal select array: array(array('value' => $value, 'label' => $label), ...)
     *  'value_option' - hashed array: array($value => $label, ...),
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    protected function _prepareValueOptions()
    {
    
    	// Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
    	$selectReady = $this->getData('value_select_options');
    	$hashedReady = $this->getData('value_option');
    
    	if ($selectReady && $hashedReady) {
    		return $this;
    	}
    
    	// Get array of select options. It will be used as source for hashed options
    	$selectOptions = null;
    	if ($this->getAttribute() === 'attribute_set_id') {
    		$entityTypeId = Mage::getSingleton('eav/config')
    		->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();
    		$selectOptions = Mage::getResourceModel('eav/entity_attribute_set_collection')
    		->setEntityTypeFilter($entityTypeId)
    		->load()
    		->toOptionArray();
    		
    	
    	} //Product custom options
    	elseif ($this->getAttribute() === 'dec_custom_options') {
    
    		$selectOptions = Mage::helper('dexconditions')->getProductCustomOptions();
    		
    	} //Product configurable options
    	elseif ($this->getAttribute() === 'dec_configurable_options') {
    
    		$selectOptions = Mage::helper('dexconditions')->getProductConfigurableOptions();
    		
    	} //Product type
    	elseif ($this->getAttribute() === 'dec_product_type') {
    
    		$selectOptions = Mage::helper('dexconditions')->getProductTypeOptions();
    	
    	} 
    	//Product inventory
    	//elseif ($this->getAttribute() === 'dec_product_stock_inventory') {
    		
    	//	$selectOptions = Mage::helper('dexconditions')->getProductTypeOptions();
        	
    	//} 
    	else if (is_object($this->getAttributeObject())) {
    		$attributeObject = $this->getAttributeObject();
    		if ($attributeObject->usesSource()) {
    			if ($attributeObject->getFrontendInput() == 'multiselect') {
    				$addEmptyOption = false;
    			} else {
    				$addEmptyOption = true;
    			}
    			$selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
    		}
    	}
    
    	// Set new values only if we really got them
    	if ($selectOptions !== null) {
    		// Overwrite only not already existing values
    		if (!$selectReady) {
    			$this->setData('value_select_options', $selectOptions);
    		}
    		if (!$hashedReady) {
    			$hashedOptions = array();
    			foreach ($selectOptions as $o) {
    				if (is_array($o['value'])) {
    					continue; // We cannot use array as index
    				}
    				$hashedOptions[$o['value']] = $o['label'];
    			}
    			$this->setData('value_option', $hashedOptions);
    		}
    	}
    
    	return $this;
    }
    
    
    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
    	if ($this->getAttribute()==='attribute_set_id') {
    		return 'select';
    	}
    	//Product custom options
    	if ($this->getAttribute()==='dec_product_type') {
    		return 'select';
    	}
    	//Product custom options
    	elseif ($this->getAttribute()==='dec_custom_options') {
    		return 'select';
    	}
    	//Product configurable options
    	elseif ($this->getAttribute()==='dec_configurable_options') {
    	    return 'select';
    	}
    	
    	
    	if (!is_object($this->getAttributeObject())) {
    		return 'text';
    	}
    	switch ($this->getAttributeObject()->getFrontendInput()) {
    		case 'select':
    		case 'boolean':
    			return 'select';
    
    		case 'multiselect':
    			return 'multiselect';
    
    		case 'date':
    			return 'date';
    
    		default:
    			return 'text';
    	}
    }
    
    public function asHtml()
    {
    	if($this->getAttribute() == 'dec_custom_options' || $this->getAttribute() == 'dec_configurable_options') {
    		$html =	$this->getTypeElementHtml()
    		.$this->getAttributeElementHtml()
    		.$this->getValueElementHtml()
    		.$this->getOperatorElementHtml()
    		.$this->getRemoveLinkHtml()
    		.$this->getChooserContainerHtml();
    
    	} else {
    		$html =	$this->getTypeElementHtml()
    		.$this->getAttributeElementHtml()
    		.$this->getOperatorElementHtml()
    		.$this->getValueElementHtml()
    		.$this->getRemoveLinkHtml()
    		.$this->getChooserContainerHtml();
    	}
    	return $html;
    }
    
    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
    	if ($this->getAttribute()==='attribute_set_id') {
    		return 'select';
    	}
    	
    	
    	if ($this->getAttribute()==='dec_custom_options') {
    		return 'dec_pco';
    	} elseif ($this->getAttribute()==='dec_configurable_options') {
    		return 'dec_pconfop';
    	} elseif ($this->getAttribute()==='dec_product_type') {
	    	return 'dec_ptype';
	    } elseif ($this->getAttribute()==='dec_product_stock_inventory') {
	    	return 'dec_pinventory';
	    } elseif ($this->getAttribute()==='dec_product_review') {
	    	return 'dec_preview';
	    }
	    
    	if (!is_object($this->getAttributeObject())) {
    		return 'string';
    	}
    	if ($this->getAttributeObject()->getAttributeCode() == 'category_ids') {
    		return 'category';
    	}
    	switch ($this->getAttributeObject()->getFrontendInput()) {
    		case 'select':
    			return 'select';
    
    		case 'multiselect':
    			return 'multiselect';
    
    		case 'date':
    			return 'date';
    
    		case 'boolean':
    			return 'boolean';
    
    		default:
    			return 'string';
    	}
    }
    
    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
    	if (null === $this->_defaultOperatorInputByType) {
    		parent::getDefaultOperatorInputByType();
    		/*
    		 * '{}' and '!{}' are left for back-compatibility and equal to '==' and '!='
    		*/
    		$this->_defaultOperatorInputByType['dec_pco'] = array('+===+','-===-');
    		$this->_defaultOperatorInputByType['dec_pconfop'] = array('+=sel=+','-=notsel=-');
    		$this->_defaultOperatorInputByType['dec_ptype'] = array('==','!=');
    		$this->_defaultOperatorInputByType['dec_pinventory'] = array('==','!=','>=','<=','>','<');
    		$this->_defaultOperatorInputByType['dec_preview'] = array('==','!=','>=','<=','>','<');
    		
    		if(Mage::helper('dexconditions')->modelRefactored()) {
    			$this->_arrayInputTypes[] = 'dec_pco';
    			$this->_arrayInputTypes[] = 'dec_pconfop';
    			$this->_arrayInputTypes[] = 'dec_ptype';
    			$this->_arrayInputTypes[] = 'dec_pinventory';
    			$this->_arrayInputTypes[] = 'dec_preview';
    		}
    		
    	}
    	return $this->_defaultOperatorInputByType;
    }
    
    /**
     * Default operator options getter
     * Provides all possible operator options
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {    	
    	if (null === $this->_defaultOperatorOptions) {
    		$this->_defaultOperatorOptions = array(
    				'+===+'  => Mage::helper('dexconditions')->__('is checked or not empty'),
    				'-===-'  => Mage::helper('dexconditions')->__('is not checked or empty'),
    				'+=sel=+'  => Mage::helper('dexconditions')->__('is selected'),
    				'-=notsel=-'  => Mage::helper('dexconditions')->__('is not selected'),
    				'=='  => Mage::helper('rule')->__('is'),
    				'!='  => Mage::helper('rule')->__('is not'),
    				'>='  => Mage::helper('rule')->__('equals or greater than'),
    				'<='  => Mage::helper('rule')->__('equals or less than'),
    				'>'   => Mage::helper('rule')->__('greater than'),
    				'<'   => Mage::helper('rule')->__('less than'),
    				'{}'  => Mage::helper('rule')->__('contains'),
    				'!{}' => Mage::helper('rule')->__('does not contain'),
    				'()'  => Mage::helper('rule')->__('is one of'),
    				'!()' => Mage::helper('rule')->__('is not one of')
    		);
    	}
    	return $this->_defaultOperatorOptions;
    }
    
    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
   
    	$product = false;
    	if ($object->getProduct() instanceof Mage_Catalog_Model_Product) {
    		$product = $object->getProduct();
    	} else {
    		$product = Mage::getModel('catalog/product')->load($object->getProductId());
    	}    	
    	
    
    	$product    	
    	->setQuoteItemQty($object->getQty())
    	->setQuoteItemPrice($object->getPrice())
    	->setQuoteItemRowTotal($object->getBaseRowTotal());
		
    	
    	
		if(Mage::helper('dexconditions')->useAdvancedConditions()) {
			$product->setDecPconfop($product);
			$product->setDecPco($product);
			
			$product->setDecPtype($product->getTypeId());
			$product->setDecProductId($product->getId());
		}
	
    
    	return parent::validate($product);
    }
    
    public function validateExtraSimple($value)
    {
    	$result = $this->validateAttribute($value);
    	return $result;   	
    }
    
    public function validateDecPconfop($configurableOptions)
    {
        $value_tmp = $this->getValueParsed();
        
        if(is_string($value_tmp))
            $value = $value_tmp;
         
        elseif(is_array($value_tmp))
            $value = $value_tmp[0];
        
        $valueArray = explode('+', $value);
        $attributeId = $valueArray[0];
        $attributeOptionId = $valueArray[1];
        
        $op = $this->getOperator();
        
        $superOptions = array();
        
        if(!count($configurableOptions))
            return false;       
        
        $result = true;
        $exist = false;
        
        foreach ($configurableOptions as $index => $optionId) {
            if($index == $attributeId && $optionId == $attributeOptionId) 
                $exist = true;
        }
        
        if($op == '+=sel=+') $result = ($exist) ? true : false;
        else $result = ($exist) ? false : true;
        
        return $result;
    }
    
    public function validateDecPco($custom_options)
    {
    	$value_tmp = $this->getValueParsed();
    	
    	if(is_string($value_tmp))
    	    $value = $value_tmp;
    	
    	elseif(is_array($value_tmp))
    	    $value = $value_tmp[0];
    	    	
       	$op = $this->getOperator();       	
       	
       	$data = explode('+=+', $value);
       	$type = $data[0];
       	$sku = $data[1];
       
       	if(!count($custom_options)) return false;
       	
       	$result = true;
       	if(Mage::getModel('catalog/product_option')->getGroupByType($type) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
       		
			$exist = false;
       		foreach ($custom_options as $option) {
  				if($option['sku'] == $sku && $option['type'] == $type)
  					$exist = true;
       		}      		
       		
       		if($op == '+===+') $result = ($exist) ? true : false;
       		else $result = ($exist) ? false : true;
       	} else {
       		$exist = false;
       		foreach ($custom_options as $option) {
       			
       			if($option['sku'] == $sku && $option['type'] == $type){
       				$exist = true;
       			}     					
       		}
       		
       		if($op == '+===+') $result = ($exist && !empty($option['value'])) ? true : false;
       		else $result = ($exist && !empty($option['value'])) ? false : true;
       	}

       	return $result;
    }

}
