<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Adminhtml_UpslabelController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('upslabel/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function showlabelAction()
    {
        $order_id = $this->getRequest()->getParam('order_id');
        $type = $this->getRequest()->getParam('type');
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $params = $this->getRequest()->getParams();
        $this->loadLayout();
        //$block = $this->getLayout()->getBlock('showlabel');
        $AccessLicenseNumber = Mage::getStoreConfig('upslabel/credentials/accesslicensenumber');
        $UserId = Mage::getStoreConfig('upslabel/credentials/userid');
        $Password = Mage::getStoreConfig('upslabel/credentials/password');
        $shipperNumber = Mage::getStoreConfig('upslabel/credentials/shippernumber');

        $lbl = Mage::getModel('upslabel/ups');
        $lbl->setCredentials($AccessLicenseNumber, $UserId, $Password, $shipperNumber);
        $collections = Mage::getModel('upslabel/upslabel');
        $colls = $collections->getCollection()->addFieldToFilter('shipment_id', $shipment_id)->addFieldToFilter('type', $type)->addFieldToFilter('status', 0);
        $coll = 0;
        foreach ($colls AS $v) {
            $coll = array_key_exists('upslabel_id', $v->getData()) ? $v['upslabel_id'] : 0;
            break;
        }
        $v = '';
        $collection = Mage::getModel('upslabel/upslabel')->load($coll);
        //print_r($colls);
        if ($collection->getShipmentId() != $shipment_id) {
            $arrPackagesOld = $this->getRequest()->getParam('package');
            foreach ($arrPackagesOld AS $k => $v) {
                $i = 0;
                foreach ($v AS $d => $f) {
                    $arrPackages[$i][$k] = $f;
                    $i += 1;
                }
            }
            unset($v, $k, $i, $d, $f);

            $lbl = $this->setParams($lbl, $params, $arrPackages);

            if ($type == 'shipment') {
                $upsl = $lbl->getShip();
                if ($params['default_return'] == 1) {
                    $lbl->serviceCode = array_key_exists('default_return_servicecode', $params) ? $params['default_return_servicecode'] : '';
                    $upsl2 = $lbl->getShipFrom();
                }
            } else if ($type == 'refund') {
                $upsl = $lbl->getShipFrom();
            } else if ($type == 'ajaxprice_shipment') {
                $upsl = $lbl->getShipPrice();
                echo $upsl;
                exit;
            } else if ($type == 'ajaxprice_refund') {
                $upsl = $lbl->getShipPriceFrom();
                echo $upsl;
                exit;
            }
            $upslabel = Mage::getModel('upslabel/upslabel');
            $colls2 = $upslabel->getCollection()->addFieldToFilter('shipment_id', $shipment_id)->addFieldToFilter('type', $type)->addFieldToFilter('status', 1);
            if (count($colls2) > 0) {
                foreach($colls2 AS $c){
                    $c->delete();
                }
            }
            if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                foreach ($upsl['arrResponsXML'] AS $upsl_one) {
                    $upslabel = Mage::getModel('upslabel/upslabel');
                    $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl_one['trackingnumber']);
                    $upslabel->setOrderId($order_id);
                    $upslabel->setShipmentId($shipment_id);
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
                    $upslabel->setOrderId($order_id);
                    $upslabel->setShipmentId($shipment_id);
                    $upslabel->setPrice($upsl['price']['price']." ".$upsl['price']['currency']);
                    $upslabel->save();
                }
                if ($params['default_return'] == 1) {
                    if (!array_key_exists('error', $upsl2) || !$upsl2['error']) {
                        foreach ($upsl2['arrResponsXML'] AS $upsl_one) {
                            $upslabel = Mage::getModel('upslabel/upslabel');
                            $upslabel->setTitle('Order ' . $order_id . ' TN' . $upsl_one['trackingnumber']);
                            $upslabel->setOrderId($order_id);
                            $upslabel->setShipmentId($shipment_id);
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
                            $upslabel->setOrderId($order_id);
                            $upslabel->setShipmentId($shipment_id);
                            $upslabel->setPrice($upsl2['price']['price']." ".$upsl2['price']['currency']);
                            $upslabel->save();
                        }
                    } else {
                        $upslabel = Mage::getModel('upslabel/upslabel');
                        $upslabel->setTitle('Order ' . $order_id);
                        $upslabel->setOrderId($order_id);
                        $upslabel->setShipmentId($shipment_id);
                        $upslabel->setType($type);
                        $upslabel->setStatustext($upsl2['errordesc']);
                        $upslabel->setStatus(1);
                        $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                        $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                        $upslabel->save();
                    }
                }
                if ($type == 'shipment') {
                    $backLink = $this->getUrl('adminhtml/sales_order_shipment/view/shipment_id/' . $shipment_id);

                } else {
                    $backLink = $this->getUrl('adminhtml/sales_order_creditmemo/view/creditmemo_id/' . $shipment_id);
                }
                if ($params['addtrack'] == 1 && $type == 'shipment') {
                    $trTitle = 'United Parcel Service';
                    $shipment = Mage::getModel('sales/order_shipment')->load($shipment_id);
                    foreach ($upsl['arrResponsXML'] AS $upsl_one1) {
                        $track = Mage::getModel('sales/order_shipment_track')
                            ->setNumber(trim($upsl_one1['trackingnumber']))
                            ->setCarrierCode('ups')
                            ->setTitle($trTitle);
                        $shipment->addTrack($track);
                    }
                    $shipment->save();
                }
                Mage::register('order_id', $order_id);
                Mage::register('shipment_id', $shipment_id);
                Mage::register('upsl', $upsl);
                if ($params['default_return'] == 1) {
                    Mage::register('upsl2', $upsl2);
                }
                Mage::register('backLink', $backLink);
                Mage::register('type', $type);
                Mage::register('error', array());
            } else {
                Mage::register('error', $upsl);
                $upslabel = Mage::getModel('upslabel/upslabel');
                $upslabel->setTitle('Order ' . $order_id);
                $upslabel->setOrderId($order_id);
                $upslabel->setShipmentId($shipment_id);
                $upslabel->setType($type);
                $upslabel->setStatustext($upsl['errordesc']);
                $upslabel->setStatus(1);
                $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                $upslabel->save();
            }
        } else {
            if ($type == 'shipment') {
                $backLink = $this->getUrl('adminhtml/sales_order_shipment/view/shipment_id/' . $shipment_id);

            } else {
                $backLink = $this->getUrl('adminhtml/sales_order_creditmemo/view/creditmemo_id/' . $shipment_id);
            }
            Mage::register('order_id', $collection->getOrderId());
            Mage::register('shipment_id', $shipment_id);
            Mage::register('upsl', $colls->getData());
            Mage::register('backLink', $backLink);
            Mage::register('type', $type);
            Mage::register('error', array());
        }
        $this->renderLayout();
    }

    public function setParams($lbl, $params, $packages)
    {
        $configOptions = new Infomodus_Upslabel_Model_Config_Options;
        $configMethod = new Infomodus_Upslabel_Model_Config_Upsmethod;
        $lbl->packages = $packages;
        $lbl->shipmentDescription = Infomodus_Upslabel_Helper_Help::escapeXML($params['shipmentdescription']);

        $lbl->shipperName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/companyname'));
        $lbl->shipperAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/attentionname'));
        $lbl->shipperPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/phonenumber'));
        $lbl->shipperAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/addressline1'));
        $lbl->shipperCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/city'));
        $lbl->shipperStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/stateprovincecode'));
        $lbl->shipperPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/postalcode'));
        $lbl->shipperCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipper_no'] . '/countrycode'));

        $lbl->shiptoCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocompanyname']);
        $lbl->shiptoAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoattentionname']);
        $lbl->shiptoPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptophonenumber']);
        $lbl->shiptoAddressLine1 = trim(Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoaddressline1']));
        $lbl->shiptoAddressLine2 = trim(Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptoaddressline2']));
        $lbl->shiptoCity = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocity']);
        $lbl->shiptoStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML($configOptions->getProvinceCode($params['shiptostateprovincecode']));
        $lbl->shiptoPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptopostalcode']);
        $lbl->shiptoCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML($params['shiptocountrycode']);
        $lbl->residentialAddress = $params['residentialaddress'];

        $lbl->shipfromCompanyName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/companyname'));
        $lbl->shipfromAttentionName = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/attentionname'));
        $lbl->shipfromPhoneNumber = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/phonenumber'));
        $lbl->shipfromAddressLine1 = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/addressline1'));
        $lbl->shipfromCity = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/city'));
        $lbl->shipfromStateProvinceCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/stateprovincecode'));
        $lbl->shipfromPostalCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/postalcode'));
        $lbl->shipfromCountryCode = Infomodus_Upslabel_Helper_Help::escapeXML(Mage::getStoreConfig('upslabel/address_' . $params['shipfrom_no'] . '/countrycode'));

        $lbl->serviceCode = array_key_exists('serviceCode', $params) ? $params['serviceCode'] : '';
        $lbl->serviceDescription = $configMethod->getUpsMethodName(array_key_exists('serviceCode', $params) ? $params['serviceCode'] : '');

        $lbl->weightUnits = array_key_exists('weightunits', $params) ? $params['weightunits'] : '';
        $lbl->weightUnitsDescription = Infomodus_Upslabel_Helper_Help::escapeXML(array_key_exists('weightunitsdescription', $params) ? $params['weightunitsdescription'] : '');

        $lbl->includeDimensions = array_key_exists('includedimensions', $params) ? $params['includedimensions'] : 0;
        $lbl->unitOfMeasurement = array_key_exists('unitofmeasurement', $params) ? $params['unitofmeasurement'] : '';
        $lbl->unitOfMeasurementDescription = Infomodus_Upslabel_Helper_Help::escapeXML(array_key_exists('unitofmeasurementdescription', $params) ? $params['unitofmeasurementdescription'] : '');

        $lbl->adult = Infomodus_Upslabel_Helper_Help::escapeXML($params['adult']);

        $lbl->codYesNo = array_key_exists('cod', $params) ? $params['cod'] : '';
        $lbl->currencyCode = array_key_exists('currencycode', $params) ? $params['currencycode'] : '';
        $lbl->codMonetaryValue = array_key_exists('codmonetaryvalue', $params) ? $params['codmonetaryvalue'] : '';
        $lbl->codFundsCode = array_key_exists('codfundscode', $params) ? $params['codfundscode'] : '';
        $lbl->carbon_neutral = array_key_exists('carbon_neutral', $params) ? $params['carbon_neutral'] : '';

        if (array_key_exists('qvn', $params) && $params['qvn'] > 0) {
            $lbl->qvn = 1;
            $lbl->qvn_code = $params['qvn_code'];
            $lbl->qvn_email_shipper = $params['qvn_email_shipper'];
            $lbl->qvn_email_shipto = $params['qvn_email_shipto'];
        }

        if ($lbl->shipfromCountryCode != $lbl->shiptoCountryCode) {
            $lbl->shipmentcharge = array_key_exists('shipmentcharge', $params) ? $params['shipmentcharge'] : 0;
        }

        if (array_key_exists('invoicelinetotalyesno', $params) && $params['invoicelinetotalyesno'] > 0) {
            $lbl->invoicelinetotal = array_key_exists('invoicelinetotal', $params) ? $params['invoicelinetotal'] : '';
        } else {
            $lbl->invoicelinetotal = '';
        }
        if ($params['upsaccount'] != 0) {
            $lbl->upsAccount = 1;
            $lbl->accountData = Mage::getModel('upslabel/account')->load($params['upsaccount']);
        }

        $lbl->adult = Infomodus_Upslabel_Helper_Help::escapeXML($params['adult']);

        $lbl->testing = $params['testing'];
        return $lbl;
    }

    public function intermediateAction()
    {

        $this->loadLayout();
        //$block = $this->getLayout()->getBlock('intermediate');
        $order_id = $this->getRequest()->getParam('order_id');
        $type = $this->getRequest()->getParam('type');
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $this->intermediatehandy($order_id, $type, $shipment_id);
        Mage::register('order', $this->imOrder);
        Mage::register('shippingAmount', $this->shippingAmount['shipping_amount']);
        Mage::register('shipment', $this->imShipment);
        Mage::register('type', $type);
        Mage::register('upsAccounts', $this->upsAccounts);
        Mage::register('shipmentTotalPrice', $this->shipmentTotalPrice);
        Mage::register('shipmentTotalWeight', $this->totalWight);
        Mage::register('shipByUps', $this->shipByUps);
        Mage::register('shipByUpsCode', $this->shipByUpsCode);
        Mage::register('shipTo', $this->shippingAddress);
        Mage::register('shipByUpsMethodName', $this->shipByUpsMethodName);
        Mage::register('shipByUpsMethods', $this->configMethod->getUpsMethods());
        Mage::register('unitofmeasurement', $this->configOptions->getUnitOfMeasurement());
        Mage::register('paymentmethod', $this->imOrder->getPayment()->getMethodInstance()->getCode());
        Mage::register('sku', $this->sku);
        Mage::register('defParams', $this->defParams);
        $this->renderLayout();
    }

    public function intermediatehandy($order_id, $type, $shipment_id)
    {
        $this->configOptions = new Infomodus_Upslabel_Model_Config_Options;
        $this->configMethod = new Infomodus_Upslabel_Model_Config_Upsmethod;

        $this->imOrder = Mage::getModel('sales/order')->load($order_id);

        $this->shippingAddress = $this->imOrder->getShippingAddress();


        $discountAmount = 0;
        $taxAmount = 0;
        if ($type == 'shipment') {
            $this->imShipment = Mage::getModel('sales/order_shipment')->load($shipment_id);
            if($this->imShipment){
                $this->shippingAmount = $this->imShipment->getShippingAddress()->getOrder()->getData();
            }
        } else {
            $this->imShipment = Mage::getModel('sales/order_creditmemo')->load($shipment_id);
        }

        $shipmentAllItems = $this->imShipment->getAllItems();
        $totalPrice = 0;
        $this->totalWight = 0;
        $totalShipmentQty = 0;
        $this->sku = array();
        foreach ($shipmentAllItems AS $item) {
            $itemData = $item->getData();
            $this->sku[] = $itemData['sku'];
            $totalPrice += $itemData['price'] * $itemData['qty'];
            $this->totalWight += $itemData['weight'] * $itemData['qty'];
            $totalShipmentQty += $itemData['qty'];
        }
        $this->sku = implode(",", $this->sku);
        $totalQty = 0;
        foreach ($this->imOrder->getAllItems() AS $item) {
            $itemData = $item->getData();
            $totalQty += $itemData['qty_ordered'];
        }
        if ($type == 'shipment') {
            $sootItems = $totalShipmentQty / $totalQty;
            if (Mage::getStoreConfig('upslabel/ratepayment/cod_discount') == 1) {
                $discountAmount = $this->shippingAmount['discount_amount'] * $sootItems;
            }
            $taxAmount = $this->shippingAmount['tax_amount'] * $sootItems;
        }
        $this->upsAccounts = array("Shipper");
        $upsAcctModel = Mage::getModel('upslabel/account')->getCollection();
        foreach ($upsAcctModel AS $key => $u1) {
            $this->upsAccounts[$key] = $u1->getCompanyname();
        }
        $this->shipmentTotalPrice = $totalPrice - abs($discountAmount) + $taxAmount;

        $ship_method = $this->imOrder->getShippingMethod();
        $this->shipByUps = preg_replace("/^ups_.{1,4}$/", 'ups', $ship_method);
        $this->shipByUpsCode = $this->configMethod->getUpsMethodNumber(preg_replace("/^ups_(.{2,4})$/", '$1', $ship_method));
        $this->shipByUpsMethodName = $this->configMethod->getUpsMethodName($this->shipByUpsCode);

        $this->defParams = array();
        $this->defParams['packagingtypecode'] = Mage::getStoreConfig('upslabel/packaging/packagingtypecode');
        $this->defParams['packagingdescription'] = Mage::getStoreConfig('upslabel/packaging/packagingdescription');
        $this->defParams['packagingreferencenumbercode'] = Mage::getStoreConfig('upslabel/packaging/packagingreferencenumbercode');
        $this->defParams['packagingreferencenumbervalue'] = Mage::getStoreConfig('upslabel/packaging/packagingreferencenumbervalue');
        $this->defParams['packagingreferencenumbercode2'] = Mage::getStoreConfig('upslabel/packaging/packagingreferencenumbercode2');
        $this->defParams['packagingreferencenumbervalue2'] = Mage::getStoreConfig('upslabel/packaging/packagingreferencenumbervalue2');
        $this->defParams['weight'] = $this->totalWight;
        $this->defParams['packweight'] = round(Mage::getStoreConfig('upslabel/weightdimension/packweight'), 1) > 0 ? round(Mage::getStoreConfig('upslabel/weightdimension/packweight'), 1) : '0';
        $this->defParams['large'] = $this->totalWight >= 90 ? '<LargePackageIndicator />' : '';
        $this->defParams['additionalhandling'] = Mage::getStoreConfig('upslabel/ratepayment/additionalhandling') == 1 ? '<AdditionalHandling />' : '';
        $this->defParams['dimansion_id'] = Mage::getStoreConfig('upslabel/weightdimension/defaultdimensionsset');
        $this->defParams['currencycode'] = Mage::getStoreConfig('upslabel/ratepayment/currencycode');
        $this->defParams['cod'] = Mage::getStoreConfig('upslabel/ratepayment/cod');
        $this->defParams['codfundscode'] = 0;
        $this->defParams['codmonetaryvalue'] = Mage::getStoreConfig('upslabel/ratepayment/cod_shipping_cost');
        $this->defParams['insuredmonetaryvalue'] = Mage::getStoreConfig('upslabel/ratepayment/insured_automaticaly') == 1 ? $this->shipmentTotalPrice : 0;

        $this->defConfRarams = array();
        $this->defConfRarams['upsaccount'] = 0;
        $this->defConfRarams['serviceCode'] = $this->shipByUps == 'ups' ? $this->shipByUpsCode : ($this->shippingAddress->getCountryId() == Mage::getStoreConfig('upslabel/address_' . Mage::getStoreConfig('upslabel/shipping/defaultshipfrom') . '/countrycode') ? Mage::getStoreConfig('upslabel/shipping/defaultshipmentmethod') : Mage::getStoreConfig('upslabel/shipping/defaultshipmentmethodworld'));
        $this->defConfRarams['shipper_no'] = Mage::getStoreConfig('upslabel/shipping/defaultshipper');
        $this->defConfRarams['shipfrom_no'] = Mage::getStoreConfig('upslabel/shipping/defaultshipfrom');
        $this->defConfRarams['testing'] = Mage::getStoreConfig('upslabel/testmode/testing');
        $this->defConfRarams['addtrack'] = Mage::getStoreConfig('upslabel/shipping/addtrack');
        $this->defConfRarams['shipmentdescription'] = Mage::getStoreConfig('upslabel/shipping/shipmentdescription') == 1 ? (Mage::helper('adminhtml')->__('Customer') . ': ' . $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname() . ' ' . Mage::helper('adminhtml')->__('Order Id') . ': ' . $this->imOrder->getIncrementId()) : (Mage::getStoreConfig('upslabel/shipping/shipmentdescription') == 2 ? Mage::helper('adminhtml')->__('Customer') . ': ' . $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname() : (Mage::getStoreConfig('upslabel/shipping/shipmentdescription') !== '' ? Mage::helper('adminhtml')->__('Order Id') . ': ' . $this->imOrder->getIncrementId() : ''));
        $this->defConfRarams['currencycode'] = Mage::getStoreConfig('upslabel/ratepayment/currencycode');
        $this->defConfRarams['shipmentcharge'] = Mage::getStoreConfig('upslabel/ratepayment/shipmentcharge');
        $this->defConfRarams['cod'] = Mage::getStoreConfig('upslabel/ratepayment/cod');
        $this->defConfRarams['codmonetaryvalue'] = Mage::getStoreConfig('upslabel/ratepayment/cod_shipping_cost') == 0 ? $this->shipmentTotalPrice : $this->shipmentTotalPrice + $this->shippingAmount['shipping_amount'];
        $this->defConfRarams['codfundscode'] = 1;
        $this->defConfRarams['invoicelinetotalyesno'] = Mage::getStoreConfig('upslabel/ratepayment/invoicelinetotal');
        $this->defConfRarams['invoicelinetotal'] = $this->shipmentTotalPrice;
        $this->defConfRarams['carbon_neutral'] = Mage::getStoreConfig('upslabel/ratepayment/carbon_neutral');
        $this->defConfRarams['default_return'] = (Mage::getStoreConfig('upslabel/return/default_return') == 0 || Mage::getStoreConfig('upslabel/return/default_return_amount') > $this->shipmentTotalPrice)?0:1;
        $this->defConfRarams['default_return_servicecode'] = Mage::getStoreConfig('upslabel/return/default_return_method');
        $this->defConfRarams['qvn'] = Mage::getStoreConfig('upslabel/quantum/qvn');
        $this->defConfRarams['qvn_code'] = explode(",", Mage::getStoreConfig('upslabel/quantum/qvn_code'));
        $this->defConfRarams['qvn_email_shipper'] = Mage::getStoreConfig('upslabel/quantum/qvn_email_shipper');
        $this->defConfRarams['adult'] = Mage::getStoreConfig('upslabel/quantum/adult');
        $this->defConfRarams['weightunits'] = Mage::getStoreConfig('upslabel/weightdimension/weightunits');
        $this->defConfRarams['largepackageindicator'] = $this->totalWight >= 90 ? '<LargePackageIndicator />' : '';
        $this->defConfRarams['includedimensions'] = Mage::getStoreConfig('upslabel/weightdimension/includedimensions');
        $this->defConfRarams['unitofmeasurement'] = Mage::getStoreConfig('upslabel/weightdimension/unitofmeasurement');
        $this->defConfRarams['residentialaddress'] = strlen($this->shippingAddress->getCompany()) > 0?'':'<ResidentialAddress />';
        $this->defConfRarams['shiptocompanyname'] = strlen($this->shippingAddress->getCompany()) > 0 ? $this->shippingAddress->getCompany() : $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname();
        $this->defConfRarams['shiptoattentionname'] = $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname();
        $this->defConfRarams['shiptophonenumber'] = $this->shippingAddress->getTelephone();
        $addressLine1 = $this->shippingAddress->getStreet();
        $this->defConfRarams['shiptoaddressline1'] = is_array($addressLine1) && array_key_exists(0, $addressLine1) ? $addressLine1[0] : $addressLine1;
        $this->defConfRarams['shiptoaddressline2'] = (is_array($addressLine1) && isset($addressLine1[1])) ? $addressLine1[1] : '';
        $this->defConfRarams['shiptocity'] = $this->shippingAddress->getCity();
        $this->defConfRarams['shiptostateprovincecode'] = $this->shippingAddress->getRegion();
        $this->defConfRarams['shiptopostalcode'] = $this->shippingAddress->getPostcode();
        $this->defConfRarams['shiptocountrycode'] = $this->shippingAddress->getCountryId();
        $this->defConfRarams['qvn_email_shipto'] = $this->shippingAddress->getEmail();
    }

    public function deletelabelAction()
    {
        $order_id = $this->getRequest()->getParam('order_id');
        $shipment_id = $this->getRequest()->getParam('shipment_id');
        $upslabel = Mage::getModel('upslabel/labelprice')->getCollection()->addFieldToFilter('order_id', $order_id)->addFieldToFilter('shipment_id', $shipment_id);
        if (count($upslabel) > 0) {
            foreach($upslabel AS $c){
                $c->delete();
            }
        }
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_label_del'));
        $this->renderLayout();
    }

    public function printAction()
    {
        $imname = $this->getRequest()->getParam('imname');
        $path_url = Mage::getBaseUrl('media') . DS . 'upslabel' . DS . 'label' . DS;
        echo '<html>
            <head>
            <title>Print Shipping Label</title>
            </head>
            <body>
            <img src="' . $path_url . $imname . '" />
            <script>
            window.onload = function(){window.print();}
            </script>
            </body>
            </html>';
        exit;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('upslabel/upslabel')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('upslabel_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('upslabel/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_edit'))
                ->_addLeft($this->getLayout()->createBlock('upslabel/adminhtml_upslabel_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabel')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('upslabel/upslabel');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('upslabel')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('upslabel')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('upslabel/upslabel');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $upslabelIds = $this->getRequest()->getParam('upslabel');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getModel('upslabel/upslabel')->load($upslabelId);
                    $upslabel->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($upslabelIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $upslabelIds = $this->getRequest()->getParam('upslabel');
        if (!is_array($upslabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($upslabelIds as $upslabelId) {
                    $upslabel = Mage::getSingleton('upslabel/upslabel')
                        ->load($upslabelId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($upslabelIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'upslabel.csv';
        $content = $this->getLayout()->createBlock('upslabel/adminhtml_upslabel_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'upslabel.xml';
        $content = $this->getLayout()->createBlock('upslabel/adminhtml_upslabel_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}