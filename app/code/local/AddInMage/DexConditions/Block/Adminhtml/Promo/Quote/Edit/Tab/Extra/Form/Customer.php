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

class AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Form_Customer extends AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Conditions
{
	
	
	public function addFormData($form, $extra_data)
	{
		$fieldset = $form->addFieldset('extra_customer_fieldset', array(
        	'legend' => Mage::helper('dexconditions')->__('Customer Specific Conditions')
        ));
		
		$name_prefix = 'extra';
		$yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
		
		$use_customer_behavior = $fieldset->addField('use_customer_behavior', 'select', array(
        		'label'     => Mage::helper('dexconditions')->__('Customer Specific Conditions'),
        		'name'      => $name_prefix.'[use_customer_behavior]',
        		'note'  	=> Mage::helper('dexconditions')->__('Enable or disable special customer conditions.'),
        		'values' 	=> Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray()
        ));
        
		
		
		
         
        $customer_action = $fieldset->addField('customer_action', 'select', array(
        		'label'     => Mage::helper('dexconditions')->__('Action'),
        		'name'      => $name_prefix.'[customer_action]',
        		'note'  	=> Mage::helper('dexconditions')->__('Choose if you want to apply this discount or not if the conditions below are met.'),
        		'values' 	=> array(
        				array('value' => 1, 'label' => Mage::helper('dexconditions')->__('Apply this discount')),
        				array('value' => 0, 'label' => Mage::helper('dexconditions')->__('Do not apply this discount'))
        		)
        ));
        
        
      
         
        $customer_condition = $fieldset->addField('customer_condition', 'select', array(
        		'label'     => '',
        		'name'      => $name_prefix.'[customer_condition]',
        		//'note'  	=> Mage::helper('dexconditions')->__('Please select the condition.'),
        		'values' 	=> array(
       							array('label' => Mage::helper('dexconditions')->__('ALL'), 'value' => array(
       								array('value' => 'all_true', 'label' => Mage::helper('dexconditions')->__('If ALL of these conditions are TRUE')),
		        					array('value' => 'all_false', 'label' => Mage::helper('dexconditions')->__('If ALL of these conditions are FALSE'))
       							)),
			       				array('label' => Mage::helper('dexconditions')->__('ANY'), 'value' => array(
			       						array('value' => 'any_true', 'label' => Mage::helper('dexconditions')->__('If ANY of these conditions are TRUE')),
			       						array('value' => 'any_false', 'label' => Mage::helper('dexconditions')->__('If ANY of these conditions are FALSE'))
			       				)),
       						),      
        ));
        
        $fieldset->addType('customer_conditions', 'AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Renderer_Customer');
        
        
        $customer_conditions = $fieldset->addField('customer_conditions', 'customer_conditions', array(
        		'label'     => '',
        		'name'      => 'customer_conditions'
        ))->setExtra($extra_data)->setRenderer($this->getLayout()->createBlock('dexconditions/adminhtml_promo_quote_edit_tab_renderer_customer_constructor'));
		
        $noncacl_action = $fieldset->addField('noncacl_action', 'select', array(
        		'label'     => Mage::helper('dexconditions')->__('If One of the Conditions Can Not Be Verified'),
        		'name'      => $name_prefix.'[noncacl_action]',
        		'note'  	=> Mage::helper('dexconditions')->__('Choose what to do if a condition can not be checked.'),
        		'values' 	=> array(
        				array('value' => 1, 'label' => Mage::helper('dexconditions')->__('Ignore undefined conditions')),
        				array('value' => 2, 'label' => Mage::helper('dexconditions')->__('Disable discount if undefined conditions were found')),
        				array('value' => 3, 'label' => Mage::helper('dexconditions')->__('Consider undefined conditions as TRUE')),
        				array('value' => 4, 'label' => Mage::helper('dexconditions')->__('Consider undefined conditions as FALSE'))
        		)
        ));
        
        $force_auth = $fieldset->addField('force_customer_auth', 'select', array(
        		'label'     => Mage::helper('dexconditions')->__('Force Customer to Log in'),
        		'name'      => $name_prefix.'[force_customer_auth]',
        		'note'  	=> Mage::helper('dexconditions')->__('Note: the conditions above will work only for authorized customers. Select \'Yes\' to force customers to log in.'),
        		'values' 	=> $yesno
        ));
		
		$dependence = $this->getFormAfter();
		
		$dependence
			->addFieldMap($use_customer_behavior->getHtmlId(), $use_customer_behavior->getName())
			->addFieldMap($customer_action->getHtmlId(), $customer_action->getName())
			->addFieldMap($noncacl_action->getHtmlId(), $noncacl_action->getName())
			->addFieldMap($customer_condition->getHtmlId(), $customer_condition->getName())
			->addFieldMap($force_auth->getHtmlId(), $force_auth->getName())
			->addFieldDependence($force_auth->getName(), $use_customer_behavior->getName(), 1)
			->addFieldDependence($customer_condition->getName(), $use_customer_behavior->getName(), 1)
			->addFieldDependence($noncacl_action->getName(), $use_customer_behavior->getName(), 1)
			->addFieldDependence($customer_action->getName(), $use_customer_behavior->getName(), 1);
			    
	}
}
