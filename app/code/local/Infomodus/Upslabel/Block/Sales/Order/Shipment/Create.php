<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Sales_Order_Shipment_Create extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create
{
    public function __construct()
    {
        Mage::log (__METHOD__);
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::__construct();

        //$this->_updateButton('save', 'label', Mage::helper('sales')->__('Submit Shipment'));
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $order_idd = $this->getRequest()->getParam('order_id');
        if ($this->getRequest()->getParam('order_id')) {
            $order = Mage::getModel('sales/order')->load($order_idd);
            $ship_method = $order->getShippingMethod();
            $shipByUps = preg_replace("/^ups_.{2,4}$/", 'ups', $ship_method);
            $onlyups = Mage::getStoreConfig('upslabel/profile/onlyups');
            $collection = Mage::getModel('upslabel/upslabel')->load($order_idd, 'order_id');
            $shipfromCountryCode = Mage::getStoreConfig('upslabel/shipfrom/countrycode');
            $shiping_adress = $order->getShippingAddress();
            $shiptoCountryCode = $shiping_adress['country_id'];
            if ($collection->getOrderId()==$order_idd || ($shipByUps == 'ups' && $shipfromCountryCode == $shiptoCountryCode)) {
                $this->_addButton('order_label', array(
                    'label' => Mage::helper('sales')->__('UPS Label'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/showlabel/order_id/' . $this->getShipment()->getOrderId()) . '\')',
                    'class' => 'go'
                ));
            }
            else if ($onlyups==0 || $shipfromCountryCode != $shiptoCountryCode) {
                $this->_addButton('order_label', array(
                    'label' => Mage::helper('sales')->__('UPS Label'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/intermediate/order_id/' . $this->getShipment()->getOrderId()) . '\')',
                    'class' => 'go'
                ));
            }
        }
    }
}
