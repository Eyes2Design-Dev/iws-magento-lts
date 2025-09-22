<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Label_Del extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected function _beforeToHtml()
    {
        $AccessLicenseNumber = Mage::getStoreConfig('upslabel/credentials/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabel/credentials/userid');
        $Password = Mage::getStoreConfig('upslabel/credentials/password');
        $shipperNumber = Mage::getStoreConfig('upslabel/credentials/shippernumber');

        $order_id = $this->getRequest()->getParam('order_id');
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $type = $this->getRequest()->getParam('type');
        $collection = Mage::getModel('upslabel/upslabel');
        $colls = $collection->getCollection()->addFieldToFilter('shipment_id', $shipment_id)->addFieldToFilter('type', $type);
        if (count($colls) > 0) {
            $coll = array();
            foreach ($colls AS $v) {
                $coll = $v;

                $lbl = new Infomodus_Upslabel_Model_Ups();

                $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
                $lbl->packagingReferenceNumberCode = Mage::getStoreConfig('upslabel/packaging/packagingreferencenumbercode');

                $result = $lbl->deleteLabel($coll['shipmentidentificationnumber']);
                if (!is_array($result)) {
                    @unlink(Mage::getBaseDir('media') . '/upslabel/label/' . $coll['labelname']);
                    @unlink(Mage::getBaseDir('media') . '/upslabel/label/' . $coll['trackingnumber'] . '.html');
                    @unlink(Mage::getBaseDir('media') . '/upslabel/label/' . "HVR" . $coll['shipmentidentificationnumber'] . ".html");
                    $collection->setId($coll->getId())->delete();
                } else {
                    echo 'Error';
                    print_r($result);
                }
            }
        }
        echo '<br />Removal was successful. Back to <a href="' . $this->getUrl('adminhtml/sales_order/view/order_id/' . $order_id) . '">order</a>.';
        if ($type == 'shipment') {
            echo ' Back to <a href="' . $this->getUrl('adminhtml/sales_order_shipment/view/shipment_id/' . $shipment_id) . '">shipment</a>';
        } else {
            echo ' Back to <a href="' . $this->getUrl('adminhtml/sales_order_creditmemo/view/creditmemo_id/' . $shipment_id) . '">credit memo</a>';
        }


        return parent::_beforeToHtml();
    }

}