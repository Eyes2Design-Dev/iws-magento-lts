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

class AddInMage_DexConditions_Block_Adminhtml_Promo_Quote_Edit_Tab_Renderer_Customer extends Varien_Data_Form_Element_Abstract
{
	protected $_addRowButtonHtml = array ();

	
	public function getElementHtml()
	{
		$customer_conditions = $this->getCustomerConditions();
		sort($customer_conditions);
		
		$html = $this->_getAddRowButtonHtml('customer');
				
		$html .= '<ul id="customer-conditions">';
		if ($customer_conditions) {
			foreach ($customer_conditions as $i => $set) {
				$values = array();
				$values['criterion'] = (isset($set['criterion'])) ? $set['criterion'] : '';
				$values['condition'] = (isset($set['condition'])) ? $set['condition'] : '';
				$values['qa'] = (isset($set['qa'])) ? $set['qa'] : '';
				$values['category'] = (isset($set['category'])) ? $set['category'] : '';
				$values['for'] = (isset($set['for'])) ? $set['for'] : 'today';
				$values['interval'] = (isset($set['interval'])) ? $set['interval'] : '';
				$values['data'] = (isset($set['data'])) ? $set['data'] : '';
				$values['gender'] = (isset($set['gender'])) ? $set['gender'] : 2;
				$values['group'] = (isset($set['group'])) ? $set['group'] : 0;
				$values['newsletter'] = (isset($set['newsletter'])) ? $set['newsletter'] : 'sub';
				$values['date'] = (isset($set['date'])) ? $set['date'] : '';				
				$values['products'] = (isset($set['products'])) ? $set['products'] : '';
				$values['customer'] = (isset($set['customer'])) ? $set['customer'] : '';
				if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
					$values['store_criterion'] = (isset($set['store_criterion'])) ? $set['store_criterion'] : 'any';
					$values['stores'] = (isset($set['stores'])) ? $set['stores'] : Mage::app()->getStore(true)->getWebsiteId();
				}
				$values['item_status_criterion'] = (isset($set['item_status_criterion'])) ? $set['item_status_criterion'] : 'any';
				$values['item_statuses'] = (isset($set['item_statuses'])) ? $set['item_statuses'] : array();
				
				$values['status_criterion'] = (isset($set['status_criterion'])) ? $set['status_criterion'] : 'any';
				$values['statuses'] = (isset($set['statuses'])) ? $set['statuses'] : array();
				$html .= $this->_getRowTemplateHtml($i, $values);
			}
		}
		$html .= '</ul>';	

	
		return $html;
    }
    
    
    protected function _getRowTemplateHtml($i = 0, $set)
    {
    	$onclick = "Element.remove($(this).up('li'));";
    	$html  = '<li>';
    	$html .= '<div class="set-container" index="'.$i.'" id="cc-steps-'.$i.'">';
    		
    	$html .= '<div class="ccondition-container">';
    	$html .= '<label></label>';
    	$html .= '<span>';
    	$html .= trim($this->getCriterionSelectHtml($i, $set['criterion']));
    	$html .= '</span>';
    	$html .= '</div>';
    	$html .= trim($this->getNextStepTemplate($i, $set));
    	$html .= '<p><a href="javascript:void(0)" onclick="'.$onclick.'">' . Mage::helper('dexconditions')->__('Remove condition') . '</a></p>';
    
    	$html .= '</div>';
    	$html  .= '</li>';
    	return $html;
    }
    
    protected function getNextStepTemplate($i, $set)
    {
    	$template = '';
    	
    	switch ($set['criterion']) {
    		case 'next_order_number':
    			$template = $this->getSalesTemplateJs($i, $set, null, 'default', false);
    			break;
    			
    		case 'orders_av_amount': 
			case 'total_income_amount':
			case 'total_revenue_amount':
			case 'total_profit_amount':
			case 'total_invoiced_amount':
			case 'total_paid_amount':
			case 'total_refunded_amount':
			case 'total_tax_amount':
			case 'total_tax_amount_actual':
			case 'total_shipping_amount':
			case 'total_shipping_amount_actual':
			case 'total_discount_amount':
			case 'total_discount_amount_actual':
			case 'total_canceled_amount':
			case 'orders_count':
			case 'total_qty_ordered':
			case 'total_qty_invoiced':
    			$template = $this->getSalesTemplateJs($i, $set);
    			break;
    			
    		case 'orders_av_amount_int': 
			case 'total_income_amount_int':
			case 'total_revenue_amount_int':
			case 'total_profit_amount_int':
			case 'total_invoiced_amount_int':
			case 'total_paid_amount_int':
			case 'total_refunded_amount_int':
			case 'total_tax_amount_int':
			case 'total_tax_amount_actual_int':
			case 'total_shipping_amount_int':
			case 'total_shipping_amount_actual_int':
			case 'total_discount_amount_int':
			case 'total_discount_amount_actual_int':
			case 'total_canceled_amount_int':
			case 'orders_count_int':
			case 'total_qty_ordered_int':
			case 'total_qty_invoiced_int':
    			$template = $this->getSalesTemplateJs($i, $set, $this->getDefaultIntervalTemplate($i, $set) ,'default');
    			break;
    		
    		case 'review_count':
    			$template = $this->getReviewCountTemplate($i, $set);
    			break;
    		case 'customer_group':
    			$template = $this->getGroupTemplate($i, $set);
    			break;
    		case 'customer_newsletter':
    			$template = $this->getNewsletterTemplate($i, $set);
    			break;
    			
    		case 'customer_first_name':
    		case 'customer_last_name':
    			$template = $this->getPersonalDataTemplate($i, $set);
    			break;
    			
    		case 'customer_sex':
    			$template = $this->getSexDataTemplate($i, $set);
    			break;
    			
    		case 'customer_birthday':
    		case 'customer_account_date':
    		case 'review_last_date':
    			$template = $this->getDateDataTemplate($i, $set);
    			break;    	
    					
    		case 'specific_product_all':
    			$template = $this->getSpecificProductTemplate($i, $set, 'all');
    			break;
    		case 'specific_product_any':
    			$template = $this->getSpecificProductTemplate($i, $set, 'any');
    			break;
    			
    		case 'cart_items_past_orders_any':
    			$template = $this->getSpecificCartProductTemplate($i, $set, 'any');
    			break;
    		case 'cart_items_past_orders_all':
    			$template = $this->getSpecificCartProductTemplate($i, $set, 'all');
    			break;
    			
    		case 'review_products':
    			$template = $this->getReviewProductTemplate($i, $set);
    			break;
    			
    		case 'specific_product_int_all':
    			$template = $this->getDefaultProductIntervalTemplate($i, $set, 'all');
    			break;
    		case 'specific_product_int_any':
    			$template = $this->getDefaultProductIntervalTemplate($i, $set, 'any');
    			break;
    			
    		case 'cart_items_past_orders_int_all':
    			$template = $this->getDefaultCartProductIntervalTemplate($i, $set, 'all');
    			break;
    		case 'cart_items_past_orders_int_any':
    			$template = $this->getDefaultCartProductIntervalTemplate($i, $set, 'any');
    			break;
    			
    		case 'specific_cat':
    			$template = $this->getSpecificCategoryTemplate($i, $set, 'default', $interval = false);
    			break;
    		case 'specific_cat_int':
    		    $template = $this->getSpecificCategoryTemplate($i, $set, 'default', $interval = true);
    		    break;
    			
    		case 'customer_specific':
    			$template = $this->getDefaultCustomerTemplate($i, $set);
    			break;
    	}
    	
    	return $template;
    }
    
    protected function getReviewProductTemplate($i, $set)
    {
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('review_product', $i, $set)).'</span>';
    	$html .= '</div>';
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex data-chooser">';
    	$html .= trim($this->getChooserInputHtml('products', $i, $set));
    	$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
    	$html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
    	$html .= '</a>';
    	$html .= '</span>';
    	$html .= trim($this->initChooser('product', $i));
    	$html .= '</div>';
    	 
    	$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
    	
    	return $html;
    }
    
    protected function getReviewCountTemplate($i, $set, $before = null, $templateType = 'default')
    {
    	$html = '';
    
    	if($before)
    		$html .= $before;
    
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType, $i, $set)).''.trim($this->getConditionInputHtml($templateType, $i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getSalesTemplateJs($i, $set, $before = null, $templateType = 'default', $showOrderStatuses = true)
    {
    	$html = '';
    
    	if($before)
    		$html .= $before;
    
    	if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
    	
    		$html .= '<div class="ccondition-container step-loaded lset">';
    		$html .= '<span '.$this->getStoresSelectHtmlVisability($set).'  id="customer-stores-lset-'.$i.'" class="lset customer-stores-group"></span>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoreConditionSelectHtml($i, $set, $all = true));
    		$html .= '</span>';
    		$html .= '</div>';
    	
    		$html .= '<div class="ccondition-container step-loaded" '.$this->getStoresSelectHtmlVisability($set).'  id="customer-stores-'.$i.'">';
    		$html .= '<label></label>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoresSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    	
    	}
    	
    	
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType, $i, $set)).''.trim($this->getConditionInputHtml($templateType, $i, $set)).'</span>';
    	$html .= '</div>';
    	
    	if($showOrderStatuses) {
    			
    		$html .= '<div id="customer-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
    		$html .= '<span '.$this->getStatusSelectorStyle($set['status_criterion']).' id="customer-statuses-lset-'.$i.'" class="lset customer-statuses-group"></span>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStatusSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    			
    		$html .= '<div class="ccondition-container step-loaded" '.$this->getStatusSelectorStyle($set['status_criterion']).' id="customer-statuses-'.$i.'">';
    		$html .= '<label></label>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStatusesSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    			
    	}
    
    	return $html;
    }
    
    protected function getStatusSelectorStyle($value)
    {
    	return ($value == 'specified') ? 'style="display:block"' : 'style="display:none"';
    }
    
    protected function getDefaultCustomerTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_specific', $i, $set)).'</span>';
    	$html .= '</div>';
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex data-chooser">';
    	$html .= trim($this->getChooserInputHtml('customer', $i, $set));
    	$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
    	$html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
    	$html .= '</a>';
    	$html .= '</span>';
    	$html .= trim($this->initChooser('customer', $i));
    	$html .= '</div>';
    	$html .= '<div class="rule-chooser" type="customer" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('customer').'"></div>';
    
    	return $html;
    }
    
    public function getCategoryChooserButton($i, $set)
    {
        $buttons = $this->getCategoryChooserConfig()->getButtons();

         
        $chooseButton = Mage::app()->getLayout()->createBlock('adminhtml/widget_button')
        ->setType('button')
        ->setId('extra_extra_customer_category_'.$i.'control')
        ->setClass('btn-chooser')
        ->setLabel($buttons['open'])
        ->setOnclick('processCategoryChooser('.$i.')');
         
        return $chooseButton->toHtml();
       
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
    
    public function getCategoryChooserHiddenElm($i, $set)
    {
        $config['name'] = 'extra[customer]['.$i.'][category]';
        $config['class'] = 'required-entry';
        $config['value'] = $set['category'];
        
        $element = new Varien_Data_Form_Element_Hidden($config);
        $element->setId('extra_customer_category_'.$i.'value');
        $element->setForm($this->getForm());
        return $element->toHtml();
    }
    
    protected function getCategoryLabel($set)
    {
        $label = Mage::helper('dexconditions')->__('Not Selected');
        
        if(isset($set['category']) && !empty($set['category'])) {
            $id = explode('/', $set['category']);
            if(isset($id[1]))
            $label = Mage::getSingleton('catalog/category')->load($id[1])->getName();
        }
        
        return $label;
    }
    
    protected function getSpecificCategoryTemplate($i, $set, $templateType = 'default', $interval = false)
    {
        $html = '<div class="ccondition-container step-loaded">';
        $html .= '<label id="extra_extra_customer_category_'.$i.'label">'.$this->getCategoryLabel($set).'</label>';
        $html .= '<div id="extra_extra_customer_category_'.$i.'advice-container" class="hidden"></div>';
        $html .= trim($this->getCategoryChooserHiddenElm($i, $set));
        $html .= trim($this->getCategoryChooserButton($i, $set));
        $html .= '</div>';
        
        if($interval) {
            $html .= '<div class="ccondition-container step-loaded">';
            $html .= '<label></label>';
            $html .= '<span class="condition-complex">'.trim($this->getForSelectHtml($i, $set)) .''. trim($this->getForInputHtml($i, $set)).'</span>';
            $html .= '</div>';
        }
        
        $html .= '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml($templateType, $i, $set)).''.trim($this->getConditionInputHtml($templateType, $i, $set)).'</span>';
        $html .= '</div>';
    
        $html .= '<div '.$this->getItemStatusesContainerVisability($set).' id="customer-item-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
        $html .= '<span '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-lset-'.$i.'" class="lset customer-item-statuses-group"></span>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
    
        $html .= '<div class="ccondition-container step-loaded" '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-'.$i.'">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusesSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
             
        return $html;
    }
    
    protected function getDefaultProductIntervalTemplate($i, $set, $type)
    {
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex data-chooser">';
    	$html .= trim($this->getChooserInputHtml('products', $i, $set));
    	$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
    	$html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
    	$html .= '</a>';
    	$html .= '</span>';
    	$html .= '</div>';
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('specific_product_int_'.$type, $i, $set)).'</span>';
    	$html .= '</div>';
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getForSelectHtml($i, $set)) .''. trim($this->getForInputHtml($i, $set)).'</span>';
    	$html .= trim($this->initChooser('product', $i));
    	$html .= '</div>';
    	
    	if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
    		 
    		$html .= '<div class="ccondition-container step-loaded lset">';
    		$html .= '<span '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-lset-'.$i.'" class="lset customer-stores-group"></span>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoreConditionSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    		 
    		$html .= '<div class="ccondition-container step-loaded" '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-'.$i.'">';
    		$html .= '<label></label>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoresSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    		 
    	}
    	 
    	$html .= '<div '.$this->getItemStatusesContainerVisability($set).' id="customer-item-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
    	$html .= '<span '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-lset-'.$i.'" class="lset customer-item-statuses-group"></span>';
    	$html .= '<span class="condition-complex">';
    	$html .= trim($this->getItemStatusSelectHtml($i, $set));
    	$html .= '</span>';
    	$html .= '</div>';
    	 
    	$html .= '<div class="ccondition-container step-loaded" '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-'.$i.'">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">';
    	$html .= trim($this->getItemStatusesSelectHtml($i, $set));
    	$html .= '</span>';
    	$html .= '</div>';
    	
    	$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
    
    	return $html;
    }
    
    protected function getDefaultCartProductIntervalTemplate($i, $set, $type)
    {
        $html = '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex data-chooser">';
        $html .= trim($this->getChooserInputHtml('products', $i, $set));
        $html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
        $html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
        $html .= '</a>';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('cart_items_past_orders_int_'.$type, $i, $set)).'</span>';
        $html .= '</div>';
        $html .= '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">'.trim($this->getForSelectHtml($i, $set)) .''. trim($this->getForInputHtml($i, $set)).'</span>';
        $html .= trim($this->initChooser('product', $i));
        $html .= '</div>';
         
        if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
             
            $html .= '<div class="ccondition-container step-loaded lset">';
            $html .= '<span '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-lset-'.$i.'" class="lset customer-stores-group"></span>';
            $html .= '<span class="condition-complex">';
            $html .= trim($this->getStoreConditionSelectHtml($i, $set));
            $html .= '</span>';
            $html .= '</div>';
             
            $html .= '<div class="ccondition-container step-loaded" '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-'.$i.'">';
            $html .= '<label></label>';
            $html .= '<span class="condition-complex">';
            $html .= trim($this->getStoresSelectHtml($i, $set));
            $html .= '</span>';
            $html .= '</div>';
             
        }
    
        $html .= '<div '.$this->getItemStatusesContainerVisability($set).' id="customer-item-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
        $html .= '<span '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-lset-'.$i.'" class="lset customer-item-statuses-group"></span>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
    
        $html .= '<div class="ccondition-container step-loaded" '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-'.$i.'">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusesSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
         
        $html .= '<div class="rule-chooser" type="product" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
    
        return $html;
    }
    
    protected function getSpecificProductTemplate($i, $set, $type)
    {
    
    	
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex data-chooser">';
    	$html .= trim($this->getChooserInputHtml('products', $i, $set));
    	$html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
    	$html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
    	$html .= '</a>';
    	$html .= '</span>';
    	$html .= trim($this->initChooser('product', $i));
    	$html .= '</div>';
    	
    	$html .= '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('specific_product_'.$type, $i, $set)).'</span>';
    	$html .= '</div>';
    	
    	if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
    	
    		$html .= '<div class="ccondition-container step-loaded lset">';
    		$html .= '<span '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-lset-'.$i.'" class="lset customer-stores-group"></span>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoreConditionSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    	
    		$html .= '<div class="ccondition-container step-loaded" '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-'.$i.'">';
    		$html .= '<label></label>';
    		$html .= '<span class="condition-complex">';
    		$html .= trim($this->getStoresSelectHtml($i, $set));
    		$html .= '</span>';
    		$html .= '</div>';
    	
    	}
    	
    	$html .= '<div '.$this->getItemStatusesContainerVisability($set).' id="customer-item-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
    	$html .= '<span '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-lset-'.$i.'" class="lset customer-item-statuses-group"></span>';
    	$html .= '<span class="condition-complex">';
    	$html .= trim($this->getItemStatusSelectHtml($i, $set));
    	$html .= '</span>';
    	$html .= '</div>';
    	
    	$html .= '<div class="ccondition-container step-loaded" '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-'.$i.'">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">';
    	$html .= trim($this->getItemStatusesSelectHtml($i, $set));
    	$html .= '</span>';
    	$html .= '</div>';
    	
    	$html .= '<div class="rule-chooser" type="product" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
    
    	return $html;
    }
    
    protected function getSpecificCartProductTemplate($i, $set, $type)
    {
    
       
        $html = '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex data-chooser">';
        $html .= trim($this->getChooserInputHtml('products', $i, $set));
        $html .= '<a href="javascript:void(0)" class="rule-chooser-trigger">';
        $html .= '<img src="'.Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif').'" class="v-middle rule-chooser-trigger" title="'.Mage::helper('dexconditions')->__('Open Chooser').'" />';
        $html .= '</a>';
        $html .= '</span>';
        $html .= trim($this->initChooser('product', $i));
        $html .= '</div>';
        
        $html .= '<div class="ccondition-container step-loaded">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('cart_items_past_orders_'.$type, $i, $set)).'</span>';
        $html .= '</div>';
         
        if(Mage::helper('dexconditions')->canShowStoreSwitcher()) {
             
            $html .= '<div class="ccondition-container step-loaded lset">';
            $html .= '<span '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-lset-'.$i.'" class="lset customer-stores-group"></span>';
            $html .= '<span class="condition-complex">';
            $html .= trim($this->getStoreConditionSelectHtml($i, $set));
            $html .= '</span>';
            $html .= '</div>';
             
            $html .= '<div class="ccondition-container step-loaded" '.$this->getStoresSelectHtmlVisability($set).' id="customer-stores-'.$i.'">';
            $html .= '<label></label>';
            $html .= '<span class="condition-complex">';
            $html .= trim($this->getStoresSelectHtml($i, $set));
            $html .= '</span>';
            $html .= '</div>';
             
        }
         
        $html .= '<div '.$this->getItemStatusesContainerVisability($set).' id="customer-item-statuses-crit-'.$i.'" class="ccondition-container step-loaded lset">';
        $html .= '<span '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-lset-'.$i.'" class="lset customer-item-statuses-group"></span>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
         
        $html .= '<div class="ccondition-container step-loaded" '.$this->getItemStatusesSelectHtmlVisability($set).' id="customer-item-statuses-'.$i.'">';
        $html .= '<label></label>';
        $html .= '<span class="condition-complex">';
        $html .= trim($this->getItemStatusesSelectHtml($i, $set));
        $html .= '</span>';
        $html .= '</div>';
         
        $html .= '<div class="rule-chooser" type="product" chooser="cc-steps-'.$i.'" id="extra_customer_'.$i.'_chooser" url="'.$this->getChooserURL('sku').'"></div>';
    
        return $html;
    }
    
    protected function getStatusesSelectHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][statuses]',
    			'class'		=> 'statuses',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getOrderStatuses(),
    			'value'		=> $set['statuses']
    	);
    
    	$element = new Varien_Data_Form_Element_Multiselect($config);
    	$element->setId('extra_customer_'.$i.'_statuses');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getStatusSelectHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][status_criterion]',
    			'class'		=> 'validate-select',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getOrderStatusValues(),
    			'onchange'	=> 'decCustomerHandleStatuses(this);',
    			'value'		=> $set['status_criterion']
    	);
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_status_criterion');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getStoresSelectHtmlVisability($set)
    {
    	return (isset($set['store_criterion']) && $set['store_criterion'] == 'specified') 
    		? '' : 'style= "display:none"'; 
    }
   
    
    protected function getItemStatusesSelectHtmlVisability($set)
    {
    	return (isset($set['item_status_criterion']) && $set['item_status_criterion'] == 'specified')
    	? '' : 'style= "display:none"';
    }
    
    protected function getItemStatusesContainerVisability($set)
    {
    	return (isset($set['condition']) && ($set['condition'] == 'not_purchased' || $set['condition'] == 'not_purchased_int'))
    	? 'style= "display:none"' : '';
    }
    
    protected function getItemStatusesSelectHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][item_statuses]',
    			'class'		=> 'statuses',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getOrderItemStatuses(),
    			'value'		=> $set['item_statuses']
    	);
    
    	$element = new Varien_Data_Form_Element_Multiselect($config);
    	$element->setId('extra_customer_'.$i.'_item_statuses');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getItemStatusSelectHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][item_status_criterion]',
    			'class'		=> 'validate-select',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getOrderItemStatusValues(),
    			'onchange'	=> 'decHandleItemStatusesC(this);',
    			'value'		=> $set['item_status_criterion']
    	);
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_item_status_criterion');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getStoreConditionSelectHtml($i, $set, $all = false)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][store_criterion]',
    			'class'		=> 'validate-select',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getStoreCritValues($all),
    			'onchange'	=> 'decHandleStoresC(this);',
    			'value'		=> $set['store_criterion']
    	);
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_store_criterion');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getStoresSelectHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][stores]',
    			'class'		=> 'validate-select',
    			'value'		=> $set['stores'],
    			'values'	=> Mage::helper('dexconditions/types_customer')->getStoreSwitcherValues()
    	);
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_stores');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    public function getChooserURL($sub)
    {
    	return Mage::getUrl('dexconditions/adminhtml_promo_widget/chooser/attribute/'.$sub.'/form/extra_extra_customer_fieldset', array('_secure' => Mage::app()->getStore()->isAdminUrlSecure(), 'isAjax' => true));
    }
    
    protected function getChooserInputHtml($subject, $i, $set)
    {
    	$config['name'] = 'extra[customer]['.$i.']['.$subject.']';
    	$config['class'] = 'rule_extra_conditions_fieldset criterion-chooser wide required-entry';
    	$config['value'] = $set[$subject];
    
    	$element = new Varien_Data_Form_Element_Text($config);
    	$element->setId('extra_customer_'.$i.'_'.$subject);
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    public function initCalendar($i)
    {
    	$html = '<script type="text/javascript">';
    	$html .= 'decProcessCalendarSetup('.$i.')';
    	$html .= '</script>';
    	
    	return $html;
    }
    
    public function initChooser($sub, $i)
    {
    	$html = '<script type="text/javascript">';
    	    	
    	if($sub == 'product') {
    		$html .= "\n";
    		$html .= 'ExtraChooserHash.set(\'extra_extra_customer_'.$i.'_products\', $H());';
    		$html .= "\n";
    		$html .= "var extra_extra_customer_fieldset = new ExtraConditionsForm($('cc-steps-".$i."'), productChooserUrl);";
    	}
    	elseif($sub == 'customer') {
    		$html .= "\n";
    		$html .= 'ExtraChooserHash.set(\'extra_extra_customer_'.$i.'_customer\', $H());';
    		$html .= "\n";
    		$html .= "var extra_extra_customer_fieldset = new ExtraConditionsForm($('cc-steps-".$i."'), customerChooserUrl);";
    	}
    	$html .= "\n";
    	$html .= '</script>';
    	 
    	return $html;
    }
    
    protected function getDateInputHtml($i, $set)
    {    	
    	$config = array(
    			'name' 			=> 'extra[customer]['.$i.'][date]',
    			'class' 		=> 'criterion-half date',
    			'value'			=> $set['date']
    	);
    	
    	if(in_array($set['condition'], Mage::helper('dexconditions/types_customer')->getConditionValuesSource(false, true)))
    		$config['style'] = 'display:none;';
    
    	$element = new Varien_Data_Form_Element_Text($config);
    	$element->setId('extra_customer_'.$i.'_date');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getImageClass($set)
    {
    	if(in_array($set['condition'], Mage::helper('dexconditions/types_customer')->getConditionValuesSource(false, true)))
    		return 'style="display:none;"';
    }
    
    protected function getDateDataTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex type-date">'.trim($this->getConditionSelectHtml($set['criterion'], $i, $set)) .''. trim($this->getDateInputHtml($i, $set)).'<img '.$this->getImageClass($set).' src="'.Mage::getDesign()->getSkinUrl('images/grid-cal.gif').'" class="v-middle" id="extra_extra_customer_'.$i.'_date_trig" title="'.Mage::helper('dexconditions')->__('Select Date').'"></span>';
    	$html .= trim($this->initCalendar($i));
    	$html .= '</div>';
    	
    	$this->_initCalendarCtrl = $i;
    
    	return $html;
    }
    
    protected function getGroupTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_group', $i, $set)) .''. trim($this->getGroupsSelectHtml($i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getNewsletterTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_newsletter', $i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getSexDataTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_sex', $i, $set)) .''. trim($this->getSexSelectHtml($i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getPersonalDataTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getConditionSelectHtml('customer_info', $i, $set)) .''. trim($this->getInfoInputHtml($i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getDefaultIntervalTemplate($i, $set)
    {
    
    	$html = '<div class="ccondition-container step-loaded">';
    	$html .= '<label></label>';
    	$html .= '<span class="condition-complex">'.trim($this->getForSelectHtml($i, $set)) .''. trim($this->getForInputHtml($i, $set)).'</span>';
    	$html .= '</div>';
    
    	return $html;
    }
    
    protected function getSexSelectHtml($i, $set)
    {
    	$config = array('name' => 'extra[customer]['.$i.'][gender]', 'class' => 'criterion-half ds', 'value' => $set['gender']);
    
    	$config['values'] = Mage::helper('dexconditions/types_customer')->getSexSelectValues();
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_gender');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    
    protected function getGroupsSelectHtml($i, $set)
    {
    	$config = array('name' => 'extra[customer]['.$i.'][group]', 'class' => 'criterion-half ds', 'value' => $set['group']);
    
    	$config['values'] = Mage::helper('dexconditions/types_customer')->getGroups();
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_group');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getInfoInputHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][data]',
    			'class' 	=> 'criterion-half',
    			'value'		=> $set['data']
    	);
    
    	$element = new Varien_Data_Form_Element_Text($config);
    	$element->setId('extra_customer_'.$i.'_data');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getForSelectHtml($i, $set)
    {
    	$config = array('name' => 'extra[customer]['.$i.'][for]', 'value' => $set['for']);
    
    	$config['values'] = Mage::helper('dexconditions/types_customer')->getForValuesSource();
    	
    	if(in_array($set['for'], Mage::helper('dexconditions/types_customer')->getForValuesSource(true, true)))
    		$config['class'] = 'criterion';
    	
    	$config['onchange']	= 'decHandleIntervals(this);';
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_for');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getForInputHtml($i, $set)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][interval]',
    			'class' 	=> 'criterion',
    			'value'		=> $set['interval']
    	);
    	
    	if(!in_array($set['for'], Mage::helper('dexconditions/types_customer')->getForValuesSource(true, true)))
    		$config['style'] = 'display:none';
    
    	$element = new Varien_Data_Form_Element_Text($config);
    	$element->setId('extra_customer_'.$i.'_interval');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getConditionSelectHtml($criterion, $i, $set)
    {
    	$config = array('name' => 'extra[customer]['.$i.'][condition]', 
    					'value' => $set['condition']);
    
    	$conditions = Mage::helper('dexconditions/types_customer')->getConditionValuesSource();
    	$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
    	
    
    	if($criterion == 'default') {
    		$config['class'] = 'criterion';
    		$config['values'] = array($conditions['=='], $conditions['!='], $conditions['>='], $conditions['<='], $conditions['>'], $conditions['<']);
    	} elseif($criterion == 'customer_info' || $criterion == 'customer_sex' || $criterion == 'customer_group') {
    		$config['class'] = 'criterion-half';
    		$config['values'] = array($conditions['=='],$conditions['!=']);
    	} elseif($criterion == 'customer_newsletter') {
			$config['values'] = array($conditions['sub'],$conditions['notsub']);
    	} elseif($criterion == 'customer_birthday' || $criterion == 'customer_account_date' || $criterion == 'review_last_date'){
    		if(!in_array($set['condition'], Mage::helper('dexconditions/types_customer')->getConditionValuesSource(false, true)))
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
    		
    		elseif($criterion == 'customer_account_date' || $criterion == 'review_last_date')
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
    	} elseif($criterion == 'specific_product_all' || $criterion == 'specific_product_any' || $criterion == 'cart_items_past_orders_any' || $criterion == 'cart_items_past_orders_all') {
    		$config['values'] = array($conditions['purchased'],$conditions['not_purchased']);
    		$config['onchange'] = 'decHandlePurchaseC(this)';
    		$config['class'] = 'validate-select';
    	} elseif($criterion == 'specific_product_int_all' || $criterion == 'specific_product_int_any' || $criterion == 'cart_items_past_orders_int_any' || $criterion == 'cart_items_past_orders_int_all') {
    		$config['values'] = array($conditions['purchased_int'],$conditions['not_purchased_int']);
    		$config['class'] = 'validate-select';
    		$config['onchange'] = 'decHandlePurchaseC(this)';
    	} elseif($criterion == 'customer_specific') {
    		$config['values'] = array($conditions['is_one'],$conditions['is_not_one']);
    		$config['class'] = 'validate-select';
    	} elseif($criterion == 'review_product') {
			$config['values'] = array($conditions['reviewed'],$conditions['not_reviewed']);
			$config['class'] = 'validate-select';
    	}
    		
    	array_unshift($config['values'], array('label' => Mage::helper('dexconditions')->__('-- Please select --'), 'value' => ''));
    	
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_condition');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getCriterionSelectHtml($i, $value)
    {
    	$config = array(
    			'name' 		=> 'extra[customer]['.$i.'][criterion]',
    			'class'		=> 'validate-select',
    			'values'	=> Mage::helper('dexconditions/types_customer')->getCriterionValuesSource(),
    			'value'		=> $value,
    			'onchange'	=> 'decLoadNextStep(this);'
    	);
    
    	$element = new Varien_Data_Form_Element_Select($config);
    	$element->setId('extra_customer_'.$i.'_criterion');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getConditionInputHtml($criterion, $i, $set)
    {
    	$config = array('name' => 'extra[customer]['.$i.'][qa]', 'class' => 'criterion', 'value' => $set['qa']);
    
    	$element = new Varien_Data_Form_Element_Text($config);
    	$element->setId('extra_customer_'.$i.'_qa');
    	$element->setForm($this->getForm());
    	return $element->toHtml();
    }
    
    protected function getCustomerConditions()
    {
    	$extra = $this->getExtra();
    	return ($extra && isset($extra['customer']) && count($extra['customer'])) ? $extra['customer'] : array();
    }
    
    
    protected function _getAddRowButtonHtml($container)
    {
    	if (! isset($this->_addRowButtonHtml [$container])) {
    		$this->_addRowButtonHtml [$container] = Mage::app()->getLayout()->createBlock('adminhtml/widget_button')
    		->setType('button')
    		->setClass('add')
    		->setLabel(Mage::helper('dexconditions')->__('Add Condition'))
    		->setOnClick("decAddCcSet();")
    		->toHtml();
    	}
    
    	return $this->_addRowButtonHtml [$container];
    }
}