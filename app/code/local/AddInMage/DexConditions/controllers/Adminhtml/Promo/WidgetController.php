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

class AddInMage_DexConditions_Adminhtml_Promo_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->getLayout()->createBlock(
                    'dexconditions/adminhtml_promo_widget_chooser_sku', 'promo_widget_chooser_sku',  array('js_form_object' => $request->getParam('form'),
                ));
                break;
                
                
            case 'customer':
               $block = $this->getLayout()->createBlock(
               		'dexconditions/adminhtml_promo_widget_chooser_customer', 'promo_widget_chooser_customer',  array('js_form_object' => $request->getParam('form'),
                ));
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
    
    public function categoryHelperAction()
    {
        $config = array();

        $id = (string)$this->getRequest()->getParam('id', 0);
        
        $config['url'] = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_category_widget/chooser', array('uniq_id' => 'extra_extra_customer_category_'.$id, 'use_massaction' => false));
        $configJson = Mage::helper('core')->jsonEncode($config);
      
        echo trim($configJson);
    }
}
