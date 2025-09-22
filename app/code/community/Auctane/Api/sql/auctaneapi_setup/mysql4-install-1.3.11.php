<?php

$installer = $this;

$installer->getConnection()->addColumn($this->getTable('sales/quote'), 'auctaneapi_discounts', 'text default NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'auctaneapi_discounts', 'text default NULL');

$installer->endSetup();