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

class AddInMage_DexConditions_Model_Customer_Sales_Report_Order_Collection extends Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
{

    protected $_customer = null;
    protected $_inited = false;

    
    /**
     * Set customer id
     *
     * @return AddInMage_DexConditions_Model_Customer_Sales_Report_Order_Updatedat_Collection
     */
    public function addCustomerFilter($customerId)
    {
    	$this->_customer = $customerId;
    	return $this;
    }
    
    /**
     * Retrieve date in UTC timezone
     *
     * @param unknown_type $date
     * @return Zend_Date|null
     */
    protected function _dateToUtc($date)
    {
        if ($date === null) {
            return null;
        }
        $dateUtc = new Zend_Date($date);
        $dateUtc->setTimezone('Etc/UTC');
        return $dateUtc;
    }
    
    /**
     * Check range dates and transforms it to strings
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _checkDates(&$from, &$to)
    {
        if ($from !== null) {
            $from = $this->formatDate($from);
        }
    
        if ($to !== null) {
            $to = $this->formatDate($to);
        }
    
        return $this;
    }
    
    /**
     * Apply customer filter
     *
     * @return AddInMage_DexConditions_Model_Customer_Sales_Report_Order_Updatedat_Collection
     */
    protected function _applyCustomerFilter($select)
    {
        if (is_null($this->_customer)) {
            return $select;
        }
        $customer = $this->_customer;
        $select->where('customer_id = ?', $customer);
        return $select;
    }
    
    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function applyOrderStatusFilter($select)
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $select->where('status IN(?)', $orderStatus);
        return $select;
    }
    
    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function applyStoresFilter($select)
    {
        if($this->_storesIds)
        return $this->_applyStoresFilterToSelect($select);
    }

    /**
     * Add selected data
     *
     * @return AddInMage_DexConditions_Model_Customer_Sales_Report_Order_Updatedat_Collection
     */
    protected function _initSelect()
    {
        if ($this->_inited) {
            return $this;
        }
        
        $from = $this->_dateToUtc($this->_from);
        $to   = $this->_dateToUtc($this->_to);

        $this->_checkDates($from, $to);
        
        $adapter = $this->getResource()->getReadConnection();
        
        // Columns list
        $columns = array(
            // convert dates from UTC to current admin timezone
            'store_id'                       => 'o.store_id',
            'order_status'                   => 'o.status',
            'orders_count'                   => new Zend_Db_Expr('COUNT(o.entity_id)'),
            'total_qty_ordered'              => new Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
            'total_qty_invoiced'             => new Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
            'total_income_amount'            => new Zend_Db_Expr(
                sprintf('SUM((%s - %s) * %s)',
                    $adapter->getIfNullSql('o.base_grand_total', 0),
                    $adapter->getIfNullSql('o.base_total_canceled',0),
                    $adapter->getIfNullSql('o.base_to_global_rate',0)
                )
            ),
            'total_revenue_amount'           => new Zend_Db_Expr(
                sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                    $adapter->getIfNullSql('o.base_total_invoiced', 0),
                    $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                    $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                    $adapter->getIfNullSql('o.base_total_refunded', 0),
                    $adapter->getIfNullSql('o.base_tax_refunded', 0),
                    $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                    $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_profit_amount'            => new Zend_Db_Expr(
            sprintf('SUM((%s - %s - %s - %s - %s) * %s)',
            $adapter->getIfNullSql('o.base_total_paid', 0),
            $adapter->getIfNullSql('o.base_total_refunded', 0),
            $adapter->getIfNullSql('o.base_tax_invoiced', 0),
            $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
            $adapter->getIfNullSql('o.base_total_invoiced_cost', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_invoiced_amount'          => new Zend_Db_Expr(
            sprintf('SUM(%s * %s)',
            $adapter->getIfNullSql('o.base_total_invoiced', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_canceled_amount'          => new Zend_Db_Expr(
            sprintf('SUM(%s * %s)',
            $adapter->getIfNullSql('o.base_total_canceled', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_paid_amount'              => new Zend_Db_Expr(
            sprintf('SUM(%s * %s)',
            $adapter->getIfNullSql('o.base_total_paid', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_refunded_amount'          => new Zend_Db_Expr(
            sprintf('SUM(%s * %s)',
            $adapter->getIfNullSql('o.base_total_refunded', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_tax_amount'               => new Zend_Db_Expr(
            sprintf('SUM((%s - %s) * %s)',
            $adapter->getIfNullSql('o.base_tax_amount', 0),
            $adapter->getIfNullSql('o.base_tax_canceled', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_tax_amount_actual'        => new Zend_Db_Expr(
            sprintf('SUM((%s -%s) * %s)',
            $adapter->getIfNullSql('o.base_tax_invoiced', 0),
            $adapter->getIfNullSql('o.base_tax_refunded', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_shipping_amount'          => new Zend_Db_Expr(
            sprintf('SUM((%s - %s) * %s)',
            $adapter->getIfNullSql('o.base_shipping_amount', 0),
            $adapter->getIfNullSql('o.base_shipping_canceled', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_shipping_amount_actual'   => new Zend_Db_Expr(
            sprintf('SUM((%s - %s) * %s)',
            $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
            $adapter->getIfNullSql('o.base_shipping_refunded', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_discount_amount'          => new Zend_Db_Expr(
            sprintf('SUM((ABS(%s) - %s) * %s)',
            $adapter->getIfNullSql('o.base_discount_amount', 0),
            $adapter->getIfNullSql('o.base_discount_canceled', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            ),
            'total_discount_amount_actual'   => new Zend_Db_Expr(
            sprintf('SUM((%s - %s) * %s)',
            $adapter->getIfNullSql('o.base_discount_invoiced', 0),
            $adapter->getIfNullSql('o.base_discount_refunded', 0),
            $adapter->getIfNullSql('o.base_to_global_rate', 0)
            )
            )
        );
  
        
        $select = $adapter->select();
        $selectOrderItem = $adapter->select();        
        $this->_applyCustomerFilter($select);
        $this->applyOrderStatusFilter($select);       
        $this->applyStoresFilter($select);
        
        if ($this->_to !== null) {
            $select->where('DATE(o.updated_at) <= DATE(?)', $to);
        }
        
        if ($this->_from !== null) {
            $select->where('DATE(o.updated_at) >= DATE(?)', $from);
        }
        
        $qtyCanceledExpr = $adapter->getIfNullSql('qty_canceled', 0);
        $cols            = array(
            'order_id'           => 'order_id',
            'total_qty_ordered'  => new Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
            'total_qty_invoiced' => new Zend_Db_Expr('SUM(qty_invoiced)'),
        );
        $selectOrderItem->from($this->getTable('sales/order_item'), $cols)
        ->where('parent_item_id IS NULL')
        ->group('order_id');
        
        $select->from(array('o' => $this->getTable('sales/order')), $columns)
        ->join(array('oi' => $selectOrderItem), 'oi.order_id = o.entity_id', array())
        ->where('o.state NOT IN (?)', array(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            Mage_Sales_Model_Order::STATE_NEW
        ));
       
        $this->_inited = true;
        return $select;
    }

    /**
     * Load
     *
     * @return AddInMage_DexConditions_Model_Customer_Sales_Report_Order_Updatedat_Collection
     */
    public function loadCustomerData()
    {
        $select = $this->_initSelect();
        $adapter = $this->getResource()->getReadConnection();
        return $adapter->fetchAll($select);
    }
}
