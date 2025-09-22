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

class AddInMage_DexConditions_Model_SalesRule_Rule_Condition_Product_Found
    extends AddInMage_DexConditions_Model_SalesRule_Rule_Condition_Product_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_found');
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1=>'FOUND',
            0=>'NOT FOUND',
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('salesrule')->__("If an item is %s in the cart with %s of these conditions true:",
            $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
           if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        $all = $this->getAggregator()==='all';
        $true = (bool)$this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        }
        // not found and we're making sure it doesn't exist
        elseif (!$found && !$true) {
            return true;
        }
        return false;
    }
}
