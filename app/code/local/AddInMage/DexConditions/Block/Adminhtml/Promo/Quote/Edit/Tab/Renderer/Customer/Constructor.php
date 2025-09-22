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

class AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Renderer_Customer_Constructor extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
	/**
	 * Initialize block template
	 */
	protected function _construct()
	{
		$this->setTemplate('addinmage/dexconditions/promo/quote/renderer/customer.phtml');
	}
	   
	protected function _getScriptTemplate()
	{
	    
		$onclick = "Element.remove($(this).up('+li+'));";
	    
		$html = '<div class="set-container" index="\'+c+\'" id="cc-steps-\'+c+\'">';
		 
		$html .= '<div class="ccondition-container">';
		$html .= '<label></label>';
		$html .= '<span>';
		$html .= trim($this->getCriterionSelectHtml());
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<p><a href="javascript:void(0)" onclick="'.$onclick.'">' . Mage::helper('dexconditions')->__('Remove condition') . '</a></p>';
	
		$html .= '</div>';
	
		return $html;
	}

	
	protected function generateTemplatesHash()
	{
		$hash = 'var customerTemplateHash = $H({';
		$hash .= 'default_sales:\''.$this->getSalesTemplateJs().'\',';
		$hash .= 'default_sales_ns:\''.$this->getSalesTemplateJs(null, 'default', false).'\',';
		$hash .= 'default_sales_interval:\''.$this->getSalesTemplateJs($this->getDefaultIntervalTemplate() ,'default').'\',';
		$hash .= 'personal_data:\''.$this->getPersonalDataTemplate().'\',';
		$hash .= 'gender:\''.$this->getSexDataTemplate().'\',';
		$hash .= 'group:\''.$this->getGroupTemplate().'\',';
		$hash .= 'newsletter:\''.$this->getNewsletterTemplate().'\',';
		$hash .= 'review_count:\''.$this->getReviewCountTemplate().'\',';
		$hash .= 'review_products:\''.$this->getReviewProductTemplate().'\',';
		$hash .= 'type_of_date:\''.$this->getDateDataTemplate().'\',';
		$hash .= 'type_of_date_acc:\''.$this->getDateDataTemplate(1).'\',';
		$hash .= 'type_of_date_rewdate:\''.$this->getDateDataTemplate(2).'\',';
		$hash .= 'specific_product_all:\''.$this->getSpecificProductTemplate('all').'\',';
		$hash .= 'specific_product_int_all:\''.$this->getDefaultProductIntervalTemplate('all').'\',';
		$hash .= 'specific_product_any:\''.$this->getSpecificProductTemplate('any').'\',';
		$hash .= 'specific_product_int_any:\''.$this->getDefaultProductIntervalTemplate('any').'\',';
		$hash .= 'specific_cat:\''.$this->getDefaultSpecificCategoryTemplate($templateType = 'default', $interval = false).'\',';
		$hash .= 'specific_cat_int:\''.$this->getDefaultSpecificCategoryTemplate($templateType = 'default', $interval = true).'\',';
		
		$hash .= 'cart_items_past_orders_all:\''.$this->getSpecificCartProductTemplate('all').'\',';
		$hash .= 'cart_items_past_orders_int_all:\''.$this->getDefaultCartProductIntervalTemplate('all').'\',';
		$hash .= 'cart_items_past_orders_any:\''.$this->getSpecificCartProductTemplate('any').'\',';
		$hash .= 'cart_items_past_orders_int_any:\''.$this->getDefaultCartProductIntervalTemplate('any').'\',';
		
		$hash .= 'customer_specific:\''.$this->getDefaultCustomerTemplate().'\'';
		$hash .= '});';
		
		return $hash;
	}
	
	
	protected function getJsInputElBehaviorHash()
	{
		$hash = 'var inputBehaviorMaps = $H({';
	
		$hash .= 'next_order_number:\'qa\',';
		$hash .= 'orders_av_amount:\'qa\','; 
		$hash .= 'total_income_amount:\'qa\',';
		$hash .= 'total_revenue_amount:\'qa\',';
		$hash .= 'total_profit_amount:\'qa\',';
		$hash .= 'total_invoiced_amount:\'qa\',';
		$hash .= 'total_paid_amount:\'qa\',';
		$hash .= 'total_refunded_amount:\'qa\',';
		$hash .= 'total_tax_amount:\'qa\',';
		$hash .= 'total_tax_amount_actual:\'qa\',';
		$hash .= 'total_shipping_amount:\'qa\',';
		$hash .= 'total_shipping_amount_actual:\'qa\',';
		$hash .= 'total_discount_amount:\'qa\',';
		$hash .= 'total_discount_amount_actual:\'qa\',';
		$hash .= 'total_canceled_amount:\'qa\',';
		$hash .= 'orders_count:\'qa\',';
		$hash .= 'total_qty_ordered:\'qa\',';
		$hash .= 'total_qty_invoiced:\'qa\',';
		$hash .= 'orders_av_amount_int:\'qa\',';
		$hash .= 'total_income_amount_int:\'qa\',';
		$hash .= 'total_revenue_amount_int:\'qa\',';
		$hash .= 'total_profit_amount_int:\'qa\',';
		$hash .= 'total_invoiced_amount_int:\'qa\',';
		$hash .= 'total_paid_amount_int:\'qa\',';
		$hash .= 'total_refunded_amount_int:\'qa\',';
		$hash .= 'total_tax_amount_int:\'qa\',';
		$hash .= 'total_tax_amount_actual_int:\'qa\',';
		$hash .= 'total_shipping_amount_int:\'qa\',';
		$hash .= 'total_shipping_amount_actual_int:\'qa\',';
		$hash .= 'total_discount_amount_int:\'qa\',';
		$hash .= 'total_discount_amount_actual_int:\'qa\',';
		$hash .= 'total_canceled_amount_int:\'qa\',';
		$hash .= 'orders_count_int:\'qa\',';
		$hash .= 'total_qty_ordered_int:\'qa\',';
		$hash .= 'total_qty_invoiced_int:\'qa\',';
		$hash .= 'specific_cat:\'qa\',';
		$hash .= 'specific_cat_int:\'qa\',';
		$hash .= 'review_count:\'qa\',';
		$hash .= 'customer_first_name:\'data\',';
		$hash .= 'customer_last_name:\'data\'';
	
		$hash .= '});';
	
		return $hash;
	}
	
	protected function getSelectIntervalsHash()
	{

		$values = Mage::helper('dexconditions/types_customer')->getCustomerPastIntValues();
		
		$hash = 'var forCustomerPastInt = $H({';
		
		$numItems = count($values);
		$i = 0;
		foreach ($values as $key => $value) {
			if(++$i === $numItems)
				$hash .= ''.$key.':\''. $value .'\'';
			else
				$hash .= ''.$key.':\''. $value .'\',';
		}
		
		$hash .= '});';
		
		return $hash;
	}
	
	protected function getSelectValueHash()
	{
		
		$values = Mage::helper('dexconditions/types_customer')->getCriterionValuesSource(false, true);
		
		$hash = 'var forCustomerValue = $H({';
		
		foreach ($values['number'] as $value) 
			$hash .= ''.$value['value'].':\''. Mage::helper('dexconditions')->__('number') .'\',';
		foreach ($values['quantity'] as $value) 
			$hash .= ''.$value['value'].':\''. Mage::helper('dexconditions')->__('quantity') .'\',';
		foreach ($values['amount'] as $value) 
			$hash .= ''.$value['value'].':\''. Mage::helper('dexconditions')->__('amount') .'\',';
		foreach ($values['first_name'] as $value) 
			$hash .= ''.$value['value'].':\''. Mage::helper('dexconditions')->__('first name') .'\',';
		foreach ($values['last_name'] as $value) 
			$hash .= ''.$value['value'].':\''. Mage::helper('dexconditions')->__('last name') .'\'';
		
		$hash .= '});';
		
		return $hash;
	}
	
	protected function getTemplateComparisonHash()
	{
		$hash = 'var customerTemplateCompHash = $H({';
		
		$hash .= 'next_order_number:\'default_sales_ns\',';
		
		$hash .= 'orders_av_amount:\'default_sales\','; 
		$hash .= 'total_income_amount:\'default_sales\',';
		$hash .= 'total_revenue_amount:\'default_sales\',';
		$hash .= 'total_profit_amount:\'default_sales\',';
		$hash .= 'total_invoiced_amount:\'default_sales\',';
		$hash .= 'total_paid_amount:\'default_sales\',';
		$hash .= 'total_refunded_amount:\'default_sales\',';
		$hash .= 'total_tax_amount:\'default_sales\',';
		$hash .= 'total_tax_amount_actual:\'default_sales\',';
		$hash .= 'total_shipping_amount:\'default_sales\',';
		$hash .= 'total_shipping_amount_actual:\'default_sales\',';
		$hash .= 'total_discount_amount:\'default_sales\',';
		$hash .= 'total_discount_amount_actual:\'default_sales\',';
		$hash .= 'total_canceled_amount:\'default_sales\',';
		$hash .= 'orders_count:\'default_sales\',';
		$hash .= 'total_qty_ordered:\'default_sales\',';
		$hash .= 'total_qty_invoiced:\'default_sales\',';
		
		$hash .= 'orders_av_amount_int:\'default_sales_interval\',';
		$hash .= 'total_income_amount_int:\'default_sales_interval\',';
		$hash .= 'total_revenue_amount_int:\'default_sales_interval\',';
		$hash .= 'total_profit_amount_int:\'default_sales_interval\',';
		$hash .= 'total_invoiced_amount_int:\'default_sales_interval\',';
		$hash .= 'total_paid_amount_int:\'default_sales_interval\',';
		$hash .= 'total_refunded_amount_int:\'default_sales_interval\',';
		$hash .= 'total_tax_amount_int:\'default_sales_interval\',';
		$hash .= 'total_tax_amount_actual_int:\'default_sales_interval\',';
		$hash .= 'total_shipping_amount_int:\'default_sales_interval\',';
		$hash .= 'total_shipping_amount_actual_int:\'default_sales_interval\',';
		$hash .= 'total_discount_amount_int:\'default_sales_interval\',';
		$hash .= 'total_discount_amount_actual_int:\'default_sales_interval\',';
		$hash .= 'total_canceled_amount_int:\'default_sales_interval\',';
		$hash .= 'orders_count_int:\'default_sales_interval\',';
		$hash .= 'total_qty_ordered_int:\'default_sales_interval\',';
		$hash .= 'total_qty_invoiced_int:\'default_sales_interval\',';
		
		$hash .= 'review_count:\'review_count\',';
		$hash .= 'review_products:\'review_products\',';
		
		$hash .= 'customer_first_name:\'personal_data\',';
		$hash .= 'customer_last_name:\'personal_data\',';
		$hash .= 'customer_sex:\'gender\',';
		$hash .= 'customer_group:\'group\',';
		$hash .= 'customer_newsletter:\'newsletter\',';
		$hash .= 'customer_birthday:\'type_of_date\',';
		$hash .= 'customer_account_date:\'type_of_date_acc\',';
		$hash .= 'review_last_date:\'type_of_date_rewdate\',';
		$hash .= 'specific_product_all:\'specific_product_all\',';
		$hash .= 'specific_product_int_all:\'specific_product_int_all\',';
		$hash .= 'specific_product_any:\'specific_product_any\',';
		$hash .= 'specific_product_int_any:\'specific_product_int_any\',';
		
		$hash .= 'cart_items_past_orders_all:\'cart_items_past_orders_all\',';
		$hash .= 'cart_items_past_orders_int_all:\'cart_items_past_orders_int_all\',';
		$hash .= 'cart_items_past_orders_any:\'cart_items_past_orders_any\',';
		$hash .= 'cart_items_past_orders_int_any:\'cart_items_past_orders_int_any\',';
		
		$hash .= 'specific_cat:\'specific_cat\',';
		$hash .= 'specific_cat_int:\'specific_cat_int\',';
		$hash .= 'customer_specific:\'customer_specific\'';
		
		$hash .= '});';
		
		return $hash;
	}
	
	protected function getDefaultCustomerTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_specific')).'</span>';
		$html .= '</div>';
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex data-chooser">';
		$html .= trim($this->getChooserInputHtml('customer'));
		$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
		$html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
		$html .= '</a>';
		$html .= '</span>';
		$html .= '</div>';
		$html .= '<div class="rule-chooser" type="customer" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('customer').'"></div>';
	
		return $html;
	}
	
	protected function getReviewProductTemplate()
	{
			
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex data-chooser">';
		$html .= trim($this->getChooserInputHtml('products'));
		$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
		$html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
		$html .= '</a>';
		$html .= '</span>';
		$html .= '</div>';
	
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('review_product')).'</span>';
		$html .= '</div>';
	
		$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
		return $html;
	}
	
	protected function getSpecificCartProductTemplate($type)
	{
	    	
	    $html = '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex data-chooser">';
	    $html .= trim($this->getChooserInputHtml('products'));
	    $html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
	    $html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
	    $html .= '</a>';
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('cart_items_past_orders_'.$type)).'</span>';
	    $html .= '</div>';
	
	    if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
	
	        $html .= '<div class="ccondition-container step-loaded lset">';
	        $html .= '<span style="display:none" id="customer-stores-lset-\'+c+\'" class="lset customer-stores-group"></span>';
	        $html .= '<span class="condition-complex">';
	        $html .= trim($this->getStoreConditionSelectHtml());
	        $html .= '</span>';
	        $html .= '</div>';
	
	        $html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-stores-\'+c+\'">';
	        $html .= '<label></label>';
	        $html .= '<span class="condition-complex">';
	        $html .= trim($this->getStoresSelectHtml());
	        $html .= '</span>';
	        $html .= '</div>';
	
	    }
	
	    $html .= '<div id="customer-item-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
	    $html .= '<span style="display:none" id="customer-item-statuses-lset-\'+c+\'" class="lset customer-item-statuses-group"></span>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-item-statuses-\'+c+\'">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusesSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
	    return $html;
	}
	
	protected function getSpecificProductTemplate($type)
	{
			
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex data-chooser">';
		$html .= trim($this->getChooserInputHtml('products'));
		$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
		$html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
		$html .= '</a>';
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('specific_product_'.$type)).'</span>';
		$html .= '</div>';
		
		if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
		
			$html .= '<div class="ccondition-container step-loaded lset">';
			$html .= '<span style="display:none" id="customer-stores-lset-\'+c+\'" class="lset customer-stores-group"></span>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoreConditionSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
		
			$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-stores-\'+c+\'">';
			$html .= '<label></label>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoresSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
		
		}
		
		$html .= '<div id="customer-item-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
		$html .= '<span style="display:none" id="customer-item-statuses-lset-\'+c+\'" class="lset customer-item-statuses-group"></span>';
		$html .= '<span class="condition-complex">';
		$html .= trim($this->getItemStatusSelectHtml());
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-item-statuses-\'+c+\'">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">';
		$html .= trim($this->getItemStatusesSelectHtml());
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
		return $html;
	}

	public function getCategoryChooserHiddenElm()
	{
	    $config = array('name' => 'extra[customer][\'+c+\'][category]', 'class' => 'required-entry');
	    
	    $element = new Varien_Data_Form_Element_Hidden($config);
	    $element->setId('extra_customer_category_\'+c+\'value');
	    $element->setForm($this->getElement()->getForm());
	    return $element->toHtml();
	}
	
	public function getCategoryChooserConfig() 
	{
	    $configArray = array('button' => array('open' => Mage::helper('dexconditions')->__('Select Category...')), 'type' => "adminhtml/catalog_category_widget_chooser");
	    
	    $config = new Varien_Object();
	    $this->setConfig($config);
	    
	    $buttons = array(
	        'open'  => Mage::helper('dexconditions')->__('Choose...'),
	        'close' => Mage::helper('dexconditions')->__('Close')
	    );
	    
	    foreach ($configArray['button'] as $id => $label) {
	        $buttons[$id] = Mage::helper('dexconditions')->__($label);
	    }
	        
	    $config->setButtons($buttons);
	    return  $this->getConfig();
	}
	
	public function getCategoryChooserButton()
	{
	    $buttons = $this->getCategoryChooserConfig()->getButtons();	   
	    
	    $chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
	    ->setType('button')
	    ->setId('extra_extra_customer_category_\'+c+\'control')
	    ->setClass('btn-chooser')
	    ->setLabel($buttons['open'])
	    ->setOnclick('processCategoryChooser(\'+c+\')');	    
	    
	    return $chooseButton->toHtml();
	}
	
	public function getCategoryHelperURL()
	{
	    return Mage::getUrl('dexconditions/adminhtml_promo_widget/categoryHelper/', array('_secure' => Mage::app()->getStore()->isAdminUrlSecure()));
	}
	
	protected function getDefaultSpecificCategoryTemplate($templateType = 'default', $interval = false)
	{
	    $html = '<div class="ccondition-container step-loaded">';
	    $html .= '<label id="extra_extra_customer_category_\'+c+\'label">'.Mage::helper('dexconditions')->__('Not Selected').'</label>';
	    $html .= '<div id="extra_extra_customer_category_\'+c+\'advice-container" class="hidden"></div>';
	    $html .= trim($this->getCategoryChooserHiddenElm());
	    $html .= trim($this->getCategoryChooserButton());
	    $html .= '</div>';	
	
	    if($interval) {
    	    $html .= '<div class="ccondition-container step-loaded">';
    	    $html .= '<label></label>';
    	    $html .= '<span class="condition-complex">'.trim($this->getForSelectHtml()) .''. trim($this->getForInputHtml()).'</span>';
    	    $html .= '</div>';
	    }
	    
	    $html .= '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType)).''.trim($this->getConditionInputHtml($templateType)).'</span>';
	    $html .= '</div>';
	
	    $html .= '<div id="customer-item-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
	    $html .= '<span style="display:none" id="customer-item-statuses-lset-\'+c+\'" class="lset customer-item-statuses-group"></span>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-item-statuses-\'+c+\'">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusesSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    //$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
	    return $html;
	}
	
	protected function getDefaultCartProductIntervalTemplate($type)
	{
	    $html = '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex data-chooser">';
	    $html .= trim($this->getChooserInputHtml('products'));
	    $html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
	    $html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
	    $html .= '</a>';
	    $html .= '</span>';
	    $html .= '</div>';
	    $html .= '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('cart_items_past_orders_int_'.$type)).'</span>';
	    $html .= '</div>';
	
	
	
	    $html .= '<div class="ccondition-container step-loaded">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">'.trim($this->getForSelectHtml()) .''. trim($this->getForInputHtml()).'</span>';
	    $html .= '</div>';
	
	
	    if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
	
	        $html .= '<div class="ccondition-container step-loaded lset">';
	        $html .= '<span style="display:none" id="customer-stores-lset-\'+c+\'" class="lset customer-stores-group"></span>';
	        $html .= '<span class="condition-complex">';
	        $html .= trim($this->getStoreConditionSelectHtml());
	        $html .= '</span>';
	        $html .= '</div>';
	
	        $html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-stores-\'+c+\'">';
	        $html .= '<label></label>';
	        $html .= '<span class="condition-complex">';
	        $html .= trim($this->getStoresSelectHtml());
	        $html .= '</span>';
	        $html .= '</div>';
	
	    }
	
	    $html .= '<div id="customer-item-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
	    $html .= '<span style="display:none" id="customer-item-statuses-lset-\'+c+\'" class="lset customer-item-statuses-group"></span>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-item-statuses-\'+c+\'">';
	    $html .= '<label></label>';
	    $html .= '<span class="condition-complex">';
	    $html .= trim($this->getItemStatusesSelectHtml());
	    $html .= '</span>';
	    $html .= '</div>';
	
	    $html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
	    return $html;
	}
	
	protected function getDefaultProductIntervalTemplate($type)
	{
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex data-chooser">';
		$html .= trim($this->getChooserInputHtml('products'));
		$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
		$html .= '<img src="'.$this->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
		$html .= '</a>';
		$html .= '</span>';
		$html .= '</div>';
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('specific_product_int_'.$type)).'</span>';
		$html .= '</div>';
		
		
		
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getForSelectHtml()) .''. trim($this->getForInputHtml()).'</span>';
		$html .= '</div>';
		
		
		if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
		
			$html .= '<div class="ccondition-container step-loaded lset">';
			$html .= '<span style="display:none" id="customer-stores-lset-\'+c+\'" class="lset customer-stores-group"></span>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoreConditionSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
		
			$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-stores-\'+c+\'">';
			$html .= '<label></label>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoresSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
		
		}
		
		$html .= '<div id="customer-item-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
		$html .= '<span style="display:none" id="customer-item-statuses-lset-\'+c+\'" class="lset customer-item-statuses-group"></span>';
		$html .= '<span class="condition-complex">';
		$html .= trim($this->getItemStatusSelectHtml());
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-item-statuses-\'+c+\'">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">';
		$html .= trim($this->getItemStatusesSelectHtml());
		$html .= '</span>';
		$html .= '</div>';
		
		$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-\'+c+\'" id="extra_customer_\'+c+\'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
	
		return $html;
	}

	
	protected function getDefaultIntervalTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getForSelectHtml()) .''. trim($this->getForInputHtml()).'</span>';
		$html .= '</div>';
	
		return $html;
	}
	
	
	protected function getSexDataTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_sex')) .''. trim($this->getSexSelectHtml()).'</span>';
		$html .= '</div>';
	
		return $html;
	}
	
	protected function getNewsletterTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_newsletter')).'</span>';
		$html .= '</div>';
	
		return $html;
	}
	
	protected function getGroupTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_group')) .''. trim($this->getGroupSelectHtml()).'</span>';
		$html .= '</div>';
	
		return $html;
	}
	
	protected function getDateDataTemplate($type = null)
	{
		$subj = 'customer_birthday';
		
		if($type == 1) $subj = 'customer_account';
		if($type == 2) $subj = 'review_last_date';
		
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex type-date">'.trim($this->getConditionSelectHtml($subj)) .''. trim($this->getDateInputHtml()).'<img src="'.$this->getSkinUrl('images/grid-cal.gif').'" class="v-middle" id="extra_extra_customer_\'+c+\'_date_trig" title="'.Mage::helper('dexconditions')->__('Select Date').'"></span>';
		$html .= '</div>';
	
		return $html;
	}
	
	protected function getPersonalDataTemplate()
	{
	
		$html = '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_info')) .''. trim($this->getInfoInputHtml()).'</span>';
		$html .= '</div>';
	
		return $html;
	}

	protected function getReviewCountTemplate($before = null, $templateType = 'default')
	{
		$html = '';
	
		if($before)
			$html .= $before;
	
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType)).''.trim($this->getConditionInputHtml($templateType)).'</span>';
		$html .= '</div>';
	
		return $html;
	}
	
	protected function getSalesTemplateJs($before = null, $templateType = 'default', $showOrderStatuses = true)
	{
		$html = '';
		
		if($before)
			$html .= $before;
		
		if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
		
			$html .= '<div class="ccondition-container step-loaded lset">';
			$html .= '<span style="display:none" id="customer-stores-lset-\'+c+\'" class="lset customer-stores-group"></span>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoreConditionSelectHtml($all = true));
			$html .= '</span>';
			$html .= '</div>';
		
			$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-stores-\'+c+\'">';
			$html .= '<label></label>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStoresSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
		
		}
		
		$html .= '<div class="ccondition-container step-loaded">';
		$html .= '<label></label>';
		$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType)).''.trim($this->getConditionInputHtml($templateType)).'</span>';
		$html .= '</div>';
		
		if($showOrderStatuses) {
			
			$html .= '<div id="customer-statuses-crit-\'+c+\'" class="ccondition-container step-loaded lset">';
			$html .= '<span style="display:none" id="customer-statuses-lset-\'+c+\'" class="lset customer-statuses-group"></span>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStatusSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
			
			$html .= '<div class="ccondition-container step-loaded" style="display:none" id="customer-statuses-\'+c+\'">';
			$html .= '<label></label>';
			$html .= '<span class="condition-complex">';
			$html .= trim($this->getStatusesSelectHtml());
			$html .= '</span>';
			$html .= '</div>';
			
		}
		
		return $html;
	}
	
	protected function getStatusesSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][statuses]',
				'class'		=> 'statuses',
				'values'	=> Mage::helper('dexconditions/types_customer')->getOrderStatuses()
		);
	
		$element = new Varien_Data_Form_Element_Multiselect($config);
		$element->setId('extra_customer_\'+c+\'_statuses');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getStatusSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][status_criterion]',
				'class'		=> 'validate-select',
				'values'	=> Mage::helper('dexconditions/types_customer')->getOrderStatusValues(),
				'onchange'	=> 'decCustomerHandleStatuses(this);'
		);
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_status_criterion');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getStoreConditionSelectHtml($all = false)
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][store_criterion]',
				'class'		=> 'validate-select',
				'values'	=> Mage::helper('dexconditions/types_customer')->getStoreCritValues($all),
				'onchange'	=> 'decHandleStoresC(this);'
		);
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_store_criterion');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getStoresSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][stores]',
				'class'		=> 'validate-select',
				'values'	=> Mage::helper('dexconditions/types_customer')->getStoreSwitcherValues()
		);
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_stores');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getItemStatusesSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][item_statuses]',
				'class'		=> 'statuses',
				'values'	=> Mage::helper('dexconditions/types_customer')->getOrderItemStatuses()
		);
	
		$element = new Varien_Data_Form_Element_Multiselect($config);
		$element->setId('extra_customer_\'+c+\'_item_statuses');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getItemStatusSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][item_status_criterion]',
				'class'		=> 'validate-select',
				'values'	=> Mage::helper('dexconditions/types_customer')->getOrderItemStatusValues(),
				'onchange'	=> 'decHandleItemStatusesC(this);'
		);
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_item_status_criterion');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getChooserInputHtml($subject)
	{		
		$config['name'] = 'extra[customer][\'+c+\']['.$subject.']';
		$config['class'] = 'rule_extra_conditions_fieldset criterion-chooser wide required-entry';
	
	
		$element = new Varien_Data_Form_Element_Text($config);
		$element->setId('extra_customer_\'+c+\'_'.$subject);
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	
	public function getChooserURL($sub)
	{
		return Mage::getUrl('dexconditions/adminhtml_promo_widget/chooser/attribute/'.$sub.'/form/extra_extra_customer_fieldset', array('_secure' => Mage::app()->getStore()->isAdminUrlSecure(), 'isAjax' => true));
	}

	
	protected function getConditionInputHtml($criterion)
	{
		$config = array('name' => 'extra[customer][\'+c+\'][qa]', 'class' => 'criterion');
	
		$element = new Varien_Data_Form_Element_Text($config);
		$element->setId('extra_customer_\'+c+\'_qa');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getDisplayDateFormat()
	{
		$outputFormat =  Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		return Varien_Date::convertZendToStrFtime($outputFormat, true, false);
	}
	
	protected function getDateInputHtml()
	{		
		$config = array(
				'name' 			=> 'extra[customer][\'+c+\'][date]', 
				'class' 		=> 'criterion-half date'
		);
	
		$element = new Varien_Data_Form_Element_Text($config);
		$element->setId('extra_customer_\'+c+\'_date');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getInfoInputHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][data]',
				'class' 	=> 'criterion-half'
		);
	
		$element = new Varien_Data_Form_Element_Text($config);
		$element->setId('extra_customer_\'+c+\'_data');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getForInputHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][interval]',
				'class' 	=> 'criterion',
				'style'		=> 'display:none'
		);
	
		$element = new Varien_Data_Form_Element_Text($config);
		$element->setId('extra_customer_\'+c+\'_interval');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getSexSelectHtml()
	{
		$config = array('name' => 'extra[customer][\'+c+\'][gender]', 'class' => 'criterion-half ds');
	
		$config['values'] = Mage::helper('dexconditions/types_customer')->getSexSelectValues();
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_gender');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getGroupSelectHtml()
	{
		$config = array('name' => 'extra[customer][\'+c+\'][group]', 'class' => 'criterion-half ds');
	
		$config['values'] = Mage::helper('dexconditions/types_customer')->getGroups();
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_group');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getForSelectHtml()
	{
		$config = array('name' => 'extra[customer][\'+c+\'][for]');
		
		$config['values'] = Mage::helper('dexconditions/types_customer')->getForValuesSource();
		
		$config['onchange']	= 'decHandleIntervals(this);';
		
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_for');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
	
	protected function getConditionSelectHtml($criterion)
	{
		$config = array('name' => 'extra[customer][\'+c+\'][condition]');
		$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
		$conditions = Mage::helper('dexconditions/types_customer')->getConditionValuesSource();
		
		
		if($criterion == 'default') {			
			$config['class'] = 'criterion';			
			$config['values'] = array($conditions['=='], $conditions['!='], $conditions['>='], $conditions['<='], $conditions['>'], $conditions['<']);
		} elseif($criterion == 'customer_info' || $criterion == 'customer_sex' || $criterion == 'customer_group') {
			$config['class'] = 'criterion-half';
			$config['values'] = array($conditions['=='],$conditions['!=']);
		} elseif($criterion == 'customer_newsletter') {
			$config['values'] = array($conditions['sub'],$conditions['notsub']);
		} elseif($criterion == 'customer_birthday' || $criterion == 'customer_account' || $criterion == 'review_last_date'){
			$config['class'] = 'criterion-half';
			if($criterion == 'customer_birthday')
				$config['values'] = array(
    									array('label' => Mage::helper('dexconditions')->__('Simple Conditions'), 'value' => array(
    										$conditions['=='], $conditions['!='], $conditions['>=d'], $conditions['<=d'],$conditions['is_na'], $conditions['is_not_na']
    									)),
    									array('label' => Mage::helper('dexconditions')->__('Advanced Conditions'), 'value' => array()),
										array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Past'), 'value' => array(
    										$conditions['yesterday'],$conditions['previous_week'],$conditions['previous_month']
										)),
										array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Present'), 'value' => array(
    										$conditions['today'],$conditions['this_week'],$conditions['this_month']
										)),
										array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Future'), 'value' => array(
    										$conditions['tomorrow'],$conditions['next_week'],$conditions['next_month']
										))
    									);
			
			elseif($criterion == 'customer_account' || $criterion == 'review_last_date')
				$config['values'] = array(
						array('label' => Mage::helper('dexconditions')->__('Simple Conditions'), 'value' => array(
								$conditions['=='], $conditions['!='], $conditions['>=d'], $conditions['<=d']
						)),
						array('label' => Mage::helper('dexconditions')->__('Advanced Conditions'), 'value' => array()),
						array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Past'), 'value' => array(
								$conditions['yesterday'],$conditions['previous_week'],$conditions['previous_month'],$conditions['previous_year']
						)),
						array('label' => str_repeat($nonEscapableNbspChar, 3) . Mage::helper('dexconditions')->__('Present'), 'value' => array(
								$conditions['today'],$conditions['this_week'],$conditions['this_month'],$conditions['this_year']
						)));
			
			$config['onchange'] = 'decHandleExDate(this);';
		} elseif($criterion == 'review_product') {
			$config['values'] = array($conditions['reviewed'],$conditions['not_reviewed']);
			$config['class'] = 'validate-select';
		} elseif($criterion == 'specific_product_all' || $criterion == 'specific_product_any' || $criterion == 'cart_items_past_orders_any' || $criterion == 'cart_items_past_orders_all') {
			$config['values'] = array($conditions['purchased'],$conditions['not_purchased']);
			$config['class'] = 'validate-select';
			$config['onchange'] = 'decHandlePurchaseC(this)';
		} elseif($criterion == 'specific_product_int_all' || $criterion == 'specific_product_int_any' || $criterion == 'cart_items_past_orders_int_all' || $criterion == 'cart_items_past_orders_int_any') {
			$config['values'] = array($conditions['purchased_int'],$conditions['not_purchased_int']);
			$config['class'] = 'validate-select';
			$config['onchange'] = 'decHandlePurchaseC(this)';
			
		} elseif($criterion == 'customer_specific') {
			$config['values'] = array($conditions['is_one'],$conditions['is_not_one']);
			$config['class'] = 'validate-select';
		}
		
		array_unshift($config['values'], array('label' => Mage::helper('dexconditions')->__('-- Please select --'), 'value' => ''));
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_condition');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}

	
	protected function getCriterionSelectHtml()
	{
		$config = array(
				'name' 		=> 'extra[customer][\'+c+\'][criterion]',
				'class'		=> 'validate-select',
				'values'	=> Mage::helper('dexconditions/types_customer')->getCriterionValuesSource(),			
				'onchange'	=> 'decLoadNextStep(this);'
		);
	
		$element = new Varien_Data_Form_Element_Select($config);
		$element->setId('extra_customer_\'+c+\'_criterion');
		$element->setForm($this->getElement()->getForm());
		return $element->toHtml();
	}
    
    /**
     * Disable field in default value using case
     *
     */
    public function checkFieldDisable()
    {
    	if ($this->canDisplayUseDefault() && $this->usedDefault()) {
    		$this->getElement()->setDisabled(true);
    	}
    	return $this;
    }
}
