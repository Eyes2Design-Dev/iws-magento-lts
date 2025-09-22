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

class AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Extra_Conditions 
extends Mage_Adminhtml_Block_Widget_Form 
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Prepare layout.
	 * Add files to use dialog windows
	 *
	 * @return Mage_Adminhtml_Block_System_Email_Template_Edit_Form
	 */
	protected function _prepareLayout()
	{
		if ($head = $this->getLayout()->getBlock('head')) {
			Mage::helper('dexconditions')->addHeadItems($head);
		}
	
		return parent::_prepareLayout();
	}
	
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('dexconditions')->__('Extra Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('dexconditions')->__('Extra Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return Mage::helper('dexconditions')->useAdvancedConditions();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
    
    public function getFormAfter()
    {
    	return $this->getLayout()->getBlock('extra_dependence');
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');        
		$extra_data = $model->getData('extra');		
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('extra_');        
        $name_prefix = 'extra';

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence', 'extra_dependence'));
        Mage::helper('dexconditions')->addFormElements($form, $extra_data);

       	$form->setValues($extra_data);    
       	
       	if ($model->isReadonly()) {
	    	foreach ($form->getElements() as $element) {
	       		$element->setReadonly(true, true);
	       	}
	    }
        
        $this->setForm($form);        
       
        Mage::dispatchEvent('adminhtml_block_salesrule_extra_conditions_prepareform', array('form' => $form));
        
        return parent::_prepareForm();
    }
}
