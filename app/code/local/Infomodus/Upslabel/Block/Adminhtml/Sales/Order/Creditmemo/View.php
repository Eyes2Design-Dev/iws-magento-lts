<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */

/**
 * Adminhtml creditmemo view
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Infomodus_Upslabel_Block_Adminhtml_Sales_Order_Creditmemo_View extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
{
    /**
     * Add & remove control buttons
     *
     */
    public function __construct()
    {
        parent::__construct();

        $shipment_id = $this->getCreditmemo()->getId();
        $order_idd = $this->getCreditmemo()->getOrderId();
        if ($shipment_id) {
            $collections = Mage::getModel('upslabel/upslabel');
            $colls = $collections->getCollection()->addFieldToFilter('shipment_id', $shipment_id)->addFieldToFilter('type', 'refund');
            $coll = 0;
            foreach ($colls AS $k => $v) {
                $coll = $k;
                break;
            }
            $collection = Mage::getModel('upslabel/upslabel')->load($coll);
            if ($collection->getShipmentId() != $shipment_id) {
                $this->_addButton('cancel', array(
                        'label' => Mage::helper('sales')->__('Ups label'),
                        'class' => 'save',
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/intermediate/order_id/' . $order_idd . '/shipment_id/' . $shipment_id . '/type/refund') . '\')'
                    )
                );
            }
            else {
                $this->_addButton('cancel', array(
                        'label' => Mage::helper('sales')->__('Ups label'),
                        'class' => 'save',
                        'onclick' => 'setLocation(\'' . $this->getUrl('upslabel/adminhtml_upslabel/showlabel/order_id/' . $order_idd . '/shipment_id/' . $shipment_id . '/type/refund') . '\')'
                    )
                );
            }
        }
    }
}