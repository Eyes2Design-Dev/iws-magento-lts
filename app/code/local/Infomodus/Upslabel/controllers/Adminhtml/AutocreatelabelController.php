<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 29.05.14
 * Time: 11:37
 */
require_once 'UpslabelController.php';
require_once 'PdflabelsController.php';

class Infomodus_Upslabel_Adminhtml_AutocreatelabelController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        try {
            $ptype = $this->getRequest()->getParam('type');
            $type = 'shipment';
            $order_ids = $this->getRequest()->getParam($ptype . '_ids');
            foreach ($order_ids AS $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
                if ($order->canShip()) {
                    $itemQty = $order->getItemsCollection()->count();
                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
                    $shipment = new Mage_Sales_Model_Order_Shipment_Api();
                    $shipmentId = $shipment->create($order->getIncrementId(), array(), '', true, true);
                    $shipmentId = Mage::getModel('sales/order_shipment')->load($shipmentId, 'increment_id')->getId();
                } else {
                    $shipment = $order->getShipmentsCollection()->getFirstItem();
                    $shipmentId = $shipment->getId();
                }
                if ($shipmentId && $shipmentId > 0) {
                    $collections = Mage::getModel('upslabel/upslabel');
                    $colls = $collections->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('shipment_id', $shipmentId)->addFieldToFilter('type', $type)->addFieldToFilter('status', 0);
                    if (count($colls) == 0) {
                        $controller = new Infomodus_Upslabel_Adminhtml_UpslabelController();
                        $controller->intermediatehandy($orderId, $type, $shipmentId);

                        $AccessLicenseNumber = Mage::getStoreConfig('upslabel/credentials/accesslicensenumber');
                        $UserId = Mage::getStoreConfig('upslabel/credentials/userid');
                        $Password = Mage::getStoreConfig('upslabel/credentials/password');
                        $shipperNumber = Mage::getStoreConfig('upslabel/credentials/shippernumber');

                        $lbl = Mage::getModel('upslabel/ups');

                        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
                        $lbl = $controller->setParams($lbl, $controller->defConfRarams, array($controller->defParams));

                        $upsl = $lbl->getShip();
                        if ($controller->defConfRarams['default_return'] == 1) {
                            $lbl->serviceCode = array_key_exists('default_return_servicecode', $controller->defConfRarams) ? $controller->defConfRarams['default_return_servicecode'] : '';
                            $upsl2 = $lbl->getShipFrom();
                        }
                        $upslabel = Mage::getModel('upslabel/upslabel');
                        $colls2 = $upslabel->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('shipment_id', $shipmentId)->addFieldToFilter('type', $type)->addFieldToFilter('status', 1);
                        if (count($colls2) > 0) {
                            foreach ($colls2 AS $c) {
                                $c->delete();
                            }
                        }
                        if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                            foreach ($upsl['arrResponsXML'] AS $upsl_one) {
                                $upslabel = Mage::getModel('upslabel/upslabel');
                                $upslabel->setTitle('Order ' . $orderId . ' TN' . $upsl_one['trackingnumber']);
                                $upslabel->setOrderId($orderId);
                                $upslabel->setShipmentId($shipmentId);
                                $upslabel->setType($type);
                                /*$upslabel->setBase64Image();*/
                                $upslabel->setTrackingnumber($upsl_one['trackingnumber']);
                                $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                                $upslabel->setShipmentdigest($upsl['digest']);
                                $upslabel->setLabelname('label' . $upsl_one['trackingnumber'] . '.gif');
                                $upslabel->setStatustext(Mage::helper('adminhtml')->__('Successfully'));
                                $upslabel->setStatus(0);
                                $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                                $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                                $upslabel->save();

                                $upslabel = Mage::getModel('upslabel/labelprice');
                                $upslabel->setOrderId($orderId);
                                $upslabel->setShipmentId($shipmentId);
                                $upslabel->setPrice($upsl['price']['price'] . " " . $upsl['price']['currency']);
                                $upslabel->save();
                            }
                            if ($controller->defConfRarams['default_return'] == 1) {
                                if (!array_key_exists('error', $upsl2) || !$upsl2['error']) {
                                    foreach ($upsl2['arrResponsXML'] AS $upsl_one) {
                                        $upslabel = Mage::getModel('upslabel/upslabel');
                                        $upslabel->setTitle('Order ' . $orderId . ' TN' . $upsl_one['trackingnumber']);
                                        $upslabel->setOrderId($orderId);
                                        $upslabel->setShipmentId($shipmentId);
                                        $upslabel->setType($type);
                                        /*$upslabel->setBase64Image();*/
                                        $upslabel->setTrackingnumber($upsl_one['trackingnumber']);
                                        $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                                        $upslabel->setShipmentdigest($upsl['digest']);
                                        $upslabel->setLabelname('label' . $upsl_one['trackingnumber'] . '.gif');
                                        $upslabel->setStatustext(Mage::helper('adminhtml')->__('Successfully'));
                                        $upslabel->setStatus(0);
                                        $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                                        $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                                        $upslabel->save();

                                        $upslabel = Mage::getModel('upslabel/labelprice');
                                        $upslabel->setOrderId($orderId);
                                        $upslabel->setShipmentId($shipmentId);
                                        $upslabel->setPrice($upsl2['price']['price'] . " " . $upsl2['price']['currency']);
                                        $upslabel->save();
                                    }
                                } else {
                                    $upslabel = Mage::getModel('upslabel/upslabel');
                                    $upslabel->setTitle('Order ' . $orderId);
                                    $upslabel->setOrderId($orderId);
                                    $upslabel->setShipmentId($shipmentId);
                                    $upslabel->setType($type);
                                    $upslabel->setStatustext($upsl2['errordesc']);
                                    $upslabel->setStatus(1);
                                    $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                                    $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                                    $upslabel->save();
                                }
                            }
                            if ($controller->defConfRarams['addtrack'] == 1 && $type == 'shipment') {
                                $trTitle = 'United Parcel Service';
                                $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
                                foreach ($upsl['arrResponsXML'] AS $upsl_one1) {
                                    $track = Mage::getModel('sales/order_shipment_track')
                                        ->setNumber(trim($upsl_one1['trackingnumber']))
                                        ->setCarrierCode('ups')
                                        ->setTitle($trTitle);
                                    $shipment->addTrack($track);
                                }
                                $shipment->save();
                            }

                        } else {
                            $upslabel = Mage::getModel('upslabel/upslabel');
                            $upslabel->setTitle('Order ' . $orderId);
                            $upslabel->setOrderId($orderId);
                            $upslabel->setShipmentId($shipmentId);
                            $upslabel->setType($type);
                            $upslabel->setStatustext($upsl['errordesc']);
                            $upslabel->setStatus(1);
                            $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                            $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                            $upslabel->save();
                        }
                    }
                }
            }
            $resp = Infomodus_Upslabel_Adminhtml_PdflabelsController::create($order_ids, $type, $ptype);
            if (!$resp) {
                $this->_redirectReferer();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }
} 