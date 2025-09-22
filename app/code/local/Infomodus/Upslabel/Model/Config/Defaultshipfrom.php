<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabel_Model_Config_Defaultshipfrom
{
    public function toOptionArray()
    {
        $c = array(
            array('label' => Mage::helper('adminhtml')->__('Ship From 1'), 'value' => 1),
            array('label' => Mage::helper('adminhtml')->__('Ship From 2'), 'value' => 2),
            array('label' => Mage::helper('adminhtml')->__('Ship From 3'), 'value' => 3),
            array('label' => Mage::helper('adminhtml')->__('Ship From 4'), 'value' => 4),
            array('label' => Mage::helper('adminhtml')->__('Ship From 5'), 'value' => 5),
        );
        return $c;
    }
}