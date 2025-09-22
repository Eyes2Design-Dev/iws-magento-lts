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
 * Txp_Express_Helper_Data
 *
 * @category   TXP
 * @package    TXP_Express_Base
 * @author     Eyes2Design LLC <eyes2design@gmail.com>
 */

class Txp_Express_Helper_Data extends Mage_Payment_Helper_Data
{
    private $ApprovalArray = array("00", "10");

    private $ErrorArray = array(
        "00" => "Approved or completed successfully",
        "01" => "Refer to card issuer",
        "02" => "Refer to card issuer, special condition",
        "03" => "Invalid merchant",
        "04" => "Pick-up card",
        "05" => "Do not honor",
        "06" => "Error",
        "07" => "Pick-up card, special condition",
        "08" => "Honor with identification",
        "10" => "Partially Approved",
        "11" => "VIP Approval",
        "12" => "Invalid transaction",
        "13" => "Invalid amount",
        "14" => "Invalid card number",
        "15" => "No such issuer",
        "16" => "Declined",
        "17" => "Customer cancellation",
        "19" => "Re-enter transaction",
        "21" => "No action taken",
        "25" => "Unable to locate record",
        "28" => "File update file locked",
        "30" => "Format error",
        "32" => "Completed partially",
        "39" => "No credit account",
        "41" => "Lost card, pick-up",
        "43" => "Stolen card, pick-up",
        "51" => "Not sufficient funds",
        "52" => "No checking account",
        "53" => "No savings account",
        "54" => "Expired card",
        "55" => "Incorrect PIN",
        "57" => "Transaction not permitted to cardholder",
        "58" => "Transaction not permitted on terminal",
        "59" => "Suspected fraud",
        "61" => "Exceeds withdrawal limit",
        "62" => "Restricted card",
        "63" => "Security violation",
        "65" => "Exceeds withdrawal frequency",
        "68" => "Response received too late",
        "69" => "Advice received too late",
        "70" => "Reserved for future use",
        "75" => "PIN tries exceeded",
        "76" => "Reversal",
        "77" => "Reversal data is inconsistent with original message.",
        "78" => "Invalid/non-existent account – Declined",
        "79" => "Already reversed",
        "80" => "No Financial Impact",
        "81" => "PIN cryptographic error found by the Visa security module during PIN decryption.",
        "82" => "Incorrect CVV",
        "83" => "Unable to verify PIN",
        "84" => "Invalid Authorization Life Cycle",
        "85" => "No reason to decline a request for Account Number Verification or Address Verification",
        "86" => "Cannot verify PIN",
        "91" => "Issuer or switch inoperative",
        "92" => "Destination Routing error",
        "93" => "Violation of law",
        "94" => "Duplicate Transmission",
        "96" => "System malfunction",
        "B1" => "Surcharge amount not permitted on Visa cards or EBT Food Stamps",
        "B2" => "Surcharge amount not supported by debit network issuer",
        "N0" => "Force STIP",
        "N3" => "Cash service not available",
        "N4" => "Cash request exceeds Issuer limit",
        "N5" => "Ineligible for re-submission",
        "N7" => "Decline for CVV2 failure",
        "N8" => "Transaction amount exceeds preauthorized approval amount",
        "P0" => "Approved; PVID code is missing, invalid, or has expired",
        "P1" => "Declined; PVID code is missing, invalid, or has expired",
        "P2" => "Invalid biller Information",
        "R0" => "The transaction was declined or returned, because the cardholder requested that payment of a specific recurring or installment payment transaction be stopped.",
        "R1" => "The transaction was declined or returned, because the cardholder requested that payment of all recurring or installment payment transactions for a specific merchant account be stopped.",
        "Q1" => "Card Authentication failed",
        "XA" => "Forward to Issuer",
        "XD" => "Forward to Issuer"
    );

    protected $extendedErrorMessage = array(
        'A400' => 'Re-auth attempt approved and settled',
        'A401' => 'Reversal approved by NID',
        'A402' => 'Reversal enqueued by NID. The reversal has been accepted, and it will be sent when the Active/Active partner becomes available. There is no financial impact until the reversal has been pulled out of the queue. A reversal is not guaranteed to occur when the Active/Active partner becomes available. However, any financial discrepancy is found through reconciliation.',
        'B400' => 'Re-auth attempt declined and original auth settled',
        'B401' => 'Re-auth attempt declined and settle amount greater than auth amount',
        'B402' => 'Re-auth attempt declined and settle amount less than auth amount',
        'B403' => 'Re-auth attempt declined and original auth expired',
        'B404' => 'Merchant closed',
        'B405' => 'Invalid input type for this merchant',
        'B406' => 'Invalid transaction type for this merchant',
        'B407' => 'Invalid product type for this merchant',
        'B408' => 'Auth and settle amounts differ and merchant not configured for managed services',
        'B40A' => 'Bad AVS result',
        'B40B' => 'Bad CVV result',
        'B40C' => 'Merchant’s closed date has passed, merchant forced closed',
        'B40D' => 'Re-auth resulted in hard decline',
        'B40E' => 'Merchant suspended',
        'B40F' => 'Invalid transaction linking',
        'B40G' => 'Original transaction already linked',
        'B40H' => 'Original transaction not found',
        'B40I' => 'Original transaction declined',
        'B40J' => 'Invalid merchant ID in linked transaction',
        'B40K' => 'Credit linked to unextracted settle transaction',
        'B40L' => 'Auth resubmission linked to approved auth',
        'B40M' => 'Merchant not configured',
        'B40N' => 'Card type sent in message did not match card type derived from routing information',
        'B40P' => 'Card security code length invalid for the card type',
        'B40Q' => 'Magnetic stripe invalid for the industry code',
        'B40R' => 'The refund amount exceeded the transaction amount',
        'B40S' => 'The void/reversal is linked to an extracted transaction',
        'B40T' => 'No account information could be found for this transaction'
    );

    protected $getAcceptedCurrencyCodes = array('USD');

    public function approvalCode()
    {
        return $this->ApprovalArray;
    }

    public function translateError($errorcode)
    {
        if ($errorcode) {
            if (array_key_exists($errorcode, $this->ErrorArray)) {
                return $this->ErrorArray[$errorcode];
            } elseif (array_key_exists($errorcode, $this->extendedErrorMessage)) {
                return $this->extendedErrorMessage[$errorcode];
            }
        }
        return "No Error Code Provided.";
    }

    /**
     * Return message for gateway transaction request
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  string $requestType
     * @param  string $lastTransactionId
     * @param  Varien_Object $card
     * @param float $amount
     * @param string $exception
     * @return bool|string
     */
    public function getTransactionMessage(
        $payment,
        $requestType,
        $lastTransactionId,
        $card,
        $amount = false,
        $exception = false
    )
    {
        return $this->getExtendedTransactionMessage(
            $payment,
            $requestType,
            $lastTransactionId,
            $card,
            $amount,
            $exception
        );
    }

    /**
     * Return message for gateway transaction request
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  string $requestType
     * @param  string $lastTransactionId
     * @param  Varien_Object $card
     * @param float $amount
     * @param string $exception
     * @param string $message Custom message, which will be added to the end of generated message
     * @param string $additionalMessage Custom message, which will be added to the end of generated message
     * @return bool|string
     */
    public function getExtendedTransactionMessage(
        $payment,
        $requestType,
        $lastTransactionId,
        $card,
        $amount = false,
        $exception = false,
        $message = false,
        $additionalMessage = false
    )
    {
        $operation = $this->_getOperation($requestType);

        if (!$operation) {
            return false;
        }

        if ($amount) {
            $amount = $this->__('amount %s', $this->_formatPrice($payment, $amount));
        }

        if ($exception) {
            $result = $this->__('failed');
        } else {
            $result = $this->__('successful');
        }

        $card = $this->__('Credit Card: xxxx-%s', $card->getCcLast4());

        $pattern = '%s %s %s - %s.';
        $texts = array($card, $amount, $operation, $result);

        if (!is_null($lastTransactionId)) {
            $pattern .= ' %s.';
            $texts[] = $this->__('Transaction ID %s', $lastTransactionId);
        }

        if ($message) {
            $pattern .= ' %s.';
            $texts[] = Mage::helper('Express')->translateError($message);
        }

        if ($additionalMessage) {
            $pattern .= ' %s.';
            $texts[] = Mage::helper('Express')->translateError($additionalMessage);
        }

        $pattern .= ' %s';
        $texts[] = ''; //$exception;

        return call_user_func_array(array($this, '__'), array_merge(array($pattern), $texts));
    }

    /**
     * Return operation name for request type
     *
     * @param  string $requestType
     * @return bool|string
     */
    protected function _getOperation($requestType)
    {
        switch ($requestType) {
            case Txp_Express_Model_Base::TYPE_AUTH_CODE:
                return $this->__('authorize');
            case Txp_Express_Model_Base::TYPE_AUTH_CAPTURE_CODE:
                return $this->__('authorize and capture');
            case Txp_Express_Model_Base::TYPE_SETTLE_CODE:
                return $this->__('capture');
            case Txp_Express_Model_Base::TYPE_CREDIT_RETURN_CODE:
                return $this->__('refund');
            case Txp_Express_Model_Base::TYPE_VOID_AUTH_CODE:
                return $this->__('void');
            default:
                return false;
        }
    }

    /**
     * Format price with currency sign
     * @param  Mage_Payment_Model_Info $payment
     * @param float $amount
     * @return string
     */
    protected function _formatPrice($payment, $amount)
    {
        return $payment->getOrder()->getBaseCurrency()->formatTxt($amount);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    /*
     * Format price for TransFirst Express
     *
     * @param float $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        return (is_numeric($amount) && $amount > 0) ? $this->_formatAmount($amount) : false;
    }


    protected function _formatAmount($amount)
    {
        return sprintf('0%s', number_format($amount, 2, '', ''));
    }

}