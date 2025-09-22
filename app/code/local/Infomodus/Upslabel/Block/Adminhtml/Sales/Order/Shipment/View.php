<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    public function __construct()
    {
        parent::__construct();
        $shipment_id = $this->getShipment()->getId();
        if ($shipment_id) {
            /*$order = Mage::getModel('sales/order')->load($this->getShipment()->getOrderId());*/
            $order_idd = $this->getShipment()->getOrderId();
            if ($order_idd) {

                $collections = Mage::getModel('upslabel/upslabel');
                $colls = $collections->getCollection()->addFieldToFilter('shipment_id',$shipment_id)->addFieldToFilter('type','shipment')->addFieldToFilter('status', 0);
                $coll=0;
                foreach($colls AS $k => $v){
                    $coll=$k;
                    break;
                }
                $collection = Mage::getModel('upslabel/upslabel')->load($coll);
                if ($collection->getShipmentId() == $shipment_id) {
                    $this->_addButton('order_label', array(
                        'label' => Mage::helper('sales')->__('UPS Label'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/showlabel/order_id/' . $order_idd . '/shipment_id/' . $shipment_id.'/type/shipment') . '\')',
                        'class' => 'go'
                    ));
                }
                else {
                    $this->_addButton('order_label', array(
                        'label' => Mage::helper('sales')->__('UPS Label'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/intermediate/order_id/' . $order_idd . '/shipment_id/' . $shipment_id.'/type/shipment') . '\')',
                        'class' => 'go'
                    ));
                }
            }
        }
    }
}
