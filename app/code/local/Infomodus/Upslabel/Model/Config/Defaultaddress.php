<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabel_Model_Config_Defaultaddress
{
    public function toOptionArray()
    {
        $c = array();
        if(Mage::getStoreConfig('upslabel/address_1/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_1/addressname')), 'value' => 1);
        }
        if(Mage::getStoreConfig('upslabel/address_2/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_2/addressname')), 'value' => 2);
        }
        if(Mage::getStoreConfig('upslabel/address_3/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_3/addressname')), 'value' => 3);
        }
        if(Mage::getStoreConfig('upslabel/address_4/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_4/addressname')), 'value' => 4);
        }
        if(Mage::getStoreConfig('upslabel/address_5/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_5/addressname')), 'value' => 5);
        }
        if(Mage::getStoreConfig('upslabel/address_6/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_6/addressname')), 'value' => 6);
        }
        if(Mage::getStoreConfig('upslabel/address_7/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_7/addressname')), 'value' => 7);
        }
        if(Mage::getStoreConfig('upslabel/address_8/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_8/addressname')), 'value' => 8);
        }
        if(Mage::getStoreConfig('upslabel/address_9/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_9/addressname')), 'value' => 9);
        }
        if(Mage::getStoreConfig('upslabel/address_10/enable')==1){
            $c[] = array('label' => Mage::helper('adminhtml')->__(Mage::getStoreConfig('upslabel/address_10/addressname')), 'value' => 10);
        }
        return $c;
    }
}