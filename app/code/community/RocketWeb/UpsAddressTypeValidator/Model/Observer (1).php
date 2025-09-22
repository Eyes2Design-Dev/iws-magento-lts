<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   RocketWeb
 * @package    RocketWeb_UpsAddressTypeValidator
 * @copyright  Copyright (c) 2013 RocketWeb (http://rocketweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     RocketWeb
 */
class RocketWeb_UpsAddressTypeValidator_Model_Observer{

    public function addResidentialIndicator(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('ups_address_validator');
        if(!$helper->doValidateAddress())
            return $this;

        $order = $observer->getEvent()->getOrder();
        $config = Mage::getModel('ups_address_validator/config');
        $shippingMethod = $order->getShippingMethod();

        if(!$config->enableIndicator() && !$config->enableIndicatorForOthers()) return $this;

        $shippingDescription  = $order->getShippingDescription();

        $session = Mage::getSingleton("core/session", array("name"=>"frontend"));
        $indicator =  $session->getData('ResidentialIndicator');

        if($indicator && !empty($indicator) && $order->getShippingAddress() && $order->getShippingAddress()->getCountryId() == 'US') {
            $indicatorText = $indicator == RocketWeb_UpsAddressTypeValidator_Model_Usa_Shipping_Carrier_Ups::ADDRESS_TYPE_RESIDENTIAL ? $config->getResidentialIndicator() : $config->getCommercialIndicator();
            $shippingDescription .= $indicatorText;
        } else {
            if($config->enableIndicator() && $config->enableIndicatorForOthers() && $order->getShippingAddress()) {
                $upsindicator = RocketWeb_UpsAddressTypeValidator_Helper_Data::getAddressTypeFromAddress($order->getShippingAddress());
                if(is_numeric($upsindicator)) {
                    $indicatorText = $upsindicator == RocketWeb_UpsAddressTypeValidator_Model_Usa_Shipping_Carrier_Ups::ADDRESS_TYPE_RESIDENTIAL ? $config->getResidentialIndicator() : $config->getCommercialIndicator();
                } else {
					$indicatorText = " - $upsindicator";
				}
                
                if(!strpos($shippingDescription, $indicatorText))
                    $shippingDescription = $shippingDescription . " " . $indicatorText;
            }
        }

        if($config->enableIndicator() && strpos($shippingMethod, 'ups_') !== false)
        {
            $order->setShippingDescription($shippingDescription);
        }
        else if ($config->enableIndicatorForOthers() && strpos($shippingMethod, 'ups_') === false)
        {
            $order->setShippingDescription($shippingDescription);
        }

        $session->unsetData('ResidentialIndicator');
        if($session->getData('invalid_address') && $order->getShippingAddress()) {
            $order->getShippingAddress()->setIsUpsInvalid(1);
        }
        $session->unsetData('invalid_address');
        return $this;
    }
}