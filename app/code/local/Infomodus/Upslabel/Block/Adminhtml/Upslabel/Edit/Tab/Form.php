<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('upslabel_form', array('legend'=>Mage::helper('upslabel')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('upslabel')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('upslabel')->__('Withdraw'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('upslabel')->__('No'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('upslabel')->__('Yes'),
              ),
          ),
      ));
     
      $fieldset->addField('order_id', 'text', array(
          'name'      => 'order_id',
          'label'     => Mage::helper('upslabel')->__('Order Id'),
          'title'     => Mage::helper('upslabel')->__('Order Id'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('labelname', 'text', array(
          'name'      => 'labelname',
          'label'     => Mage::helper('upslabel')->__('Label Name'),
          'title'     => Mage::helper('upslabel')->__('Label Name'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('shipmentidentificationnumber', 'text', array(
          'name'      => 'shipmentidentificationnumber',
          'label'     => Mage::helper('upslabel')->__('Shipment Identification Number'),
          'title'     => Mage::helper('upslabel')->__('Shipment Identification Number'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('trackingnumber', 'text', array(
          'name'      => 'trackingnumber',
          'label'     => Mage::helper('upslabel')->__('Tracking Number'),
          'title'     => Mage::helper('upslabel')->__('Tracking Number'),
          'required'  => true,
          'readonly' => true,
      ));
      $fieldset->addField('shipmentdigest', 'text', array(
          'name'      => 'shipmentdigest',
          'label'     => Mage::helper('upslabel')->__('Shipment Digest'),
          'title'     => Mage::helper('upslabel')->__('Shipment Digest'),
          'required'  => true,
          'readonly' => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getUpslabelData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getUpslabelData());
          Mage::getSingleton('adminhtml/session')->setUpslabelData(null);
      } elseif ( Mage::registry('upslabel_data') ) {
          $form->setValues(Mage::registry('upslabel_data')->getData());
      }
      return parent::_prepareForm();
  }
}