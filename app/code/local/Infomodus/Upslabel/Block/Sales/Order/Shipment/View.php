<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    public function __construct()
    {
        parent::__construct();
        $ship_id=$this->getShipment()->getId();
        if ($ship_id) {
            $order = Mage::getModel('sales/order')->load($this->getShipment()->getOrderId());
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            $onlyups = Mage::getStoreConfig('upslabel/profile/onlyups');
            if ($shipByUps == 'ups' || $onlyups==0) {
                $this->_addButton('order_label', array(
                    'label' => Mage::helper('sales')->__('UPS Label'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/showlabel/order_id/' . $this->getShipment()->getOrderId(). '/new/no/ship_id/'.$ship_id) .'\')',
                    'class' => 'go'
                ));
            }
        }
    }
}
