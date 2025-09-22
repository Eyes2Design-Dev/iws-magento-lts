<?php
/**
 * Add In Mage::
 *
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the EULA that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: http://add-in-mage.com/support/presales/eula/
 *
 *
 * PROPRIETARY DATA
 * 
 * This file contains trade secret data which is the property of Add In Mage:: Ltd. 
 * This file is submitted to recipient in confidence.
 * Information and source code contained herein may not be used, copied, sold, distributed, 
 * sub-licensed, rented, leased or disclosed in whole or in part to anyone except as permitted by written
 * agreement signed by an officer of Add In Mage:: Ltd.
 * 
 * 
 * MAGENTO EDITION NOTICE
 * 
 * This software is designed for Magento Community edition.
 * Add In Mage:: Ltd. does not guarantee correct work of this extension on any other Magento edition.
 * Add In Mage:: Ltd. does not provide extension support in case of using a different Magento edition.
 * 
 * 
 * @category    AddInMage
 * @package     AddInMage_DexConditions
 * @copyright   Copyright (c) 2013 Add In Mage:: Ltd. (http://www.add-in-mage.com)
 * @license     http://add-in-mage.com/support/presales/eula/  End User License Agreement (EULA)
 * @author      Add In Mage:: Team <team@add-in-mage.com>
 */

class AddInMage_DexConditions_Block_Adminhtml_Promo_Widget_Chooser_Customer extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct($arguments=array())
    {
        parent::__construct($arguments);

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('customerChooserGrid_'.$this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
        $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
        $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function _addColumnFilterToCollection($column)
    {

        if ($column->getId() == 'in_customers') {
            $selected = $this->_getSelectedCustomers();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return AddInMage_DexConditions_Block_Adminhtml_Promo_Widget_Chooser_Customer
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);
  
        return parent::_prepareCollection();
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return AddInMage_DexConditions_Block_Adminhtml_Promo_Widget_Chooser_Customer
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_customers', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_customers',
            'values'    => $this->_getSelectedCustomers(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'use_index' => true,
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
        	'sortable'  => true,
        	'width'     => '55px',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('name', array(
        		'header'    => Mage::helper('customer')->__('Name'),
        		'index'     => 'name'
        ));        
        
        $this->addColumn('email', array(
        		'header'    => Mage::helper('customer')->__('Email'),
        		'index'     => 'email'
        ));
        
        $this->addColumn('billing_country_id', array(
        		'header'    => Mage::helper('customer')->__('Country'),
        		'type'      => 'country',
        		'width'     => '120px',
        		'index'     => 'billing_country_id',
        ));
        
   
        if (!Mage::app()->isSingleStoreMode()) {
        	$this->addColumn('website_id', array(
        			'header'    => Mage::helper('customer')->__('Website'),
        			'type'      => 'options',
        			'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
        			'index'     => 'website_id',
        	));
        }
        
        $groups = Mage::getResourceModel('customer/group_collection')
        ->addFieldToFilter('customer_group_id', array('gt'=> 0))
        ->load()
        ->toOptionHash();
        
        $this->addColumn('group', array(
        		'header'    =>  Mage::helper('customer')->__('Group'),
        		'index'     =>  'group_id',
        		'type'      =>  'options',
        		'align'     => 	'left',
        		'options'   =>  $groups,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/chooser', array(
            '_current'          => true,
            'current_grid_id'   => $this->getId(),
            'collapse'          => null
        ));
    }

    protected function _getSelectedCustomers()
    {
        $customers = $this->getRequest()->getPost('selected', array());

        return $customers;
    }

}

