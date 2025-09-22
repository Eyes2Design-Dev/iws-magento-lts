<?php
/**
 * EYES2DESIGN - "TRANSACTION EXPRESS" PAYMENT GATEWAY EXTENSION
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EYES2DESIGN "TRANSACTION EXPRESS"
 * PAYMENT GATEWAY EXTENSION License, which extends
 * the Open Software License (OSL 3.0). The License is available at this URL:
 * http://www.eyes2design.com/licenses/txp-license.pdf
 * The Open Software License is available at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, EYES2DESIGN is not held
 * liable for any inconsistencies or abnormalities in the behaviour of this code.
 * By adding to, editing, or in any way modifying this code,
 * the Licensee terminates any agreement of support offered by EYES2DESIGN,
 * outlined in the provided License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time EYES2DESIGN spent
 * during the support process.
 * EYES2DESIGN does not guarantee compatibility with any other framework extension.
 * EYES2DESIGN is not responsible for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * eyes2design@gmail.com or call (385)-215-4181, so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2013 Eyes2Design LLC (http://www.www.eyes2design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Txp_Express_Model_Base_Source_Order_Status
 *
 * @category   TXP
 * @package    TXP_Express_Base
 * @author     Eyes2Design LLC <eyes2design@gmail.com>
 */

class Txp_Express_Model_Base_Source_Order_Status extends Mage_Adminhtml_Model_System_Config_Source_Order_Status{

    protected $_stateStatuses = array(
        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        Mage_Sales_Model_Order::STATE_PROCESSING
    );
}