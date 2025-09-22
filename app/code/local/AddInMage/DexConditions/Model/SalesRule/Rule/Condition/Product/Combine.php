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

class AddInMage_DexConditions_Model_SalesRule_Rule_Condition_Product_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_combine');
    }

    public function getNewChildSelectOptions()
    {
    	
        $productCondition = Mage::getModel('salesrule/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        $iAttributes = array();
        $decAttributes = array();
        foreach ($productAttributes as $code=>$label) {
            if (strpos($code, 'quote_item_')===0) {
            	
                $iAttributes[] = array('value'=>'salesrule/rule_condition_product|'.$code, 'label'=>$label);
                
            } 
            elseif(strpos($code, 'dec_')===0) {
            	
            	$decAttributes[] = array('value'=>'salesrule/rule_condition_product|'.$code, 'label'=>$label);
            	
            } 
            else {
                $pAttributes[] = array('value'=>'salesrule/rule_condition_product|'.$code, 'label'=>$label);
            }
        }
        

        
        $groups = array(
        	array('value'=>'salesrule/rule_condition_product_combine', 'label'=>Mage::helper('catalog')->__('Conditions Combination')),
        	array('label'=>Mage::helper('catalog')->__('Cart Item Attribute'), 'value'=>$iAttributes),
        	array('label'=>Mage::helper('catalog')->__('Product Attribute'), 'value'=>$pAttributes));
        
        if(Mage::helper('dexconditions')->useAdvancedConditions())
        $groups[] = array('label'=>Mage::helper('dexconditions')->__('Extra Conditions'), 'value'=>$decAttributes);
        

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions,$groups);
    	
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
    	
        foreach ($this->getConditions() as $condition) {
        	
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
