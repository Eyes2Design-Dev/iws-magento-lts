<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
	ALTER TABLE `{$this->getTable('admin/user')}` MODIFY `customer_group_id` varchar(255);
");

$this->endSetup();	