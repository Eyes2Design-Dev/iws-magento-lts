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
 * Txp_Express_Model_Base
 *
 * @category   TXP
 * @package    TXP_Express_Base
 * @author     Eyes2Design LLC <eyes2design@gmail.com>
 */

class Txp_Express_Model_Base extends Mage_Payment_Model_Method_Cc
{
    /**
     * Transfirst Express Gateway Url
     */
    protected $WsdlUrl;

    protected function getWsdlUrl($service = null)
    {
        if ($service) {
            if (Mage::getStoreConfig('payment/TxpBase/test_account')) {
                $serviceWsdl = 'https://ws.cert.processnow.com/portal/merchantframework/MerchantWebServices-v1?wsdl';
                $eReportWsdl = 'https://tfreports.cert.processnow.com/eReports/eReportsService.svc?wsdl';
            } else {
                $serviceWsdl = 'https://ws.processnow.com/portal/merchantframework/MerchantWebServices-v1?wsdl';
                $eReportWsdl = 'https://tfreports.processnow.com/eReports/eReportsService.svc?wsdl';
            }
            if ($service == self::ISREPORT) {
                $this->WsdlUrl = $eReportWsdl;
            }
            if ($service == self::ISSERVICE) {
                $this->WsdlUrl = $serviceWsdl;
            }
        }
        return $this->WsdlUrl;
    }

    protected function merchant()
    {
        return array(
            'id' => (Mage::getStoreConfig('payment/TxpBase/TXPAccountNumber')),
            'regKey' => (Mage::getStoreConfig('payment/TxpBase/TXPAccountPassword')),
            'inType' => 1
        );
    }

    const METHOD_CODE = 'TxpBase';
    const TYPE_AUTH_CODE = 0;
    const TYPE_AUTH_CAPTURE_CODE = 1;
    const TYPE_SETTLE_CODE = 3;
    const TYPE_CREDIT_RETURN_CODE = 4;
    const TYPE_VOID_CODE = 6;
    const TYPE_ACCOUNT_VERIFICATION_CODE = 9;

    /* eReport Const */
    const E_REPORT_GET_TRANACTION = 101;
    const ISSERVICE = 'service';
    const ISREPORT = 'report';

    const PARTIAL_AUTH_LAST_SUCCESS = 'last_success';
    const PARTIAL_AUTH_LAST_DECLINED = 'last_declined';
    const PARTIAL_AUTH_ALL_CANCELED = 'all_canceled';
    const PARTIAL_AUTH_CARDS_LIMIT_EXCEEDED = 'card_limit_exceeded';
    const PARTIAL_AUTH_DATA_CHANGED = 'data_changed';

    protected $_code = self::METHOD_CODE;

    /**
     * Availability Payment Method features
     * @var bool
     */
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid = false;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_allowCurrencyCode = "USD";

    protected $_Request;
    protected $_Response;
    protected $_Client;
    protected $_TransactionType;

    /**
     * Retrieve model helper
     *
     * @return Mage_Payment_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Express');
    }

    /**
     * Key for storing transaction id in additional information of payment model
     * @var string
     */
    protected $_realTransactionIdKey = 'real_transaction_id';

    /**
     * Key for storing split tender id in additional information of payment model
     * @var string
     */
    protected $_splitTenderIdKey = 'split_tender_id';

    /**
     * Key for storing locking gateway actions flag in additional information of payment model
     * @var string
     */
    protected $_isGatewayActionsLockedKey = 'is_gateway_actions_locked';

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        if ($this->_isPreauthorizeCapture($this->getInfoInstance())) {
            return true;
        }

        /**
         * If there are no transactions, it is placing order and capturing is available
         */
        foreach ($this->getCardsStorage()->getCards() as $card) {
            $lastTransaction = $this->getInfoInstance()->getTransaction($card->getLastTransId());
            if ($lastTransaction) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check refund availability
     *
     * @return bool
     * TODO: NOT Complete
     */
    /*    public function canRefund()
        {
            if ($this->getCardsStorage()->getCardsCount() <= 0) {
                return false;
            }

            foreach ($this->getCardsStorage()->getCards() as $card) {
                $lastTransaction = $this->getInfoInstance()->getTransaction($card->getLastTransId());
                $this->_eReport($card);
                if ($lastTransaction
                    && !$lastTransaction->getIsClosed()
                    && (preg_match('/Settled/i', $this->getResults()->GetTransactionResult->Status))
                ) {
                    return true;
                }
            }
            return false;
        }*/

    /**
     * Check void availability
     *
     * @param   Varien_Object $invoicePayment
     * @return  bool
     * TODO: NOT Complete
     */
    /*    public function canVoid(Varien_Object $payment)
        {
            if ($this->getCardsStorage()->getCardsCount() <= 0) {
                return false;
            }

            foreach ($this->getCardsStorage()->getCards() as $card) {
                $lastTransaction = $this->getInfoInstance()->getTransaction($card->getLastTransId());
                $this->_eReport($card);
                if ($lastTransaction
                    && !$lastTransaction->getIsClosed()
                    && preg_match('/ApprovedNotSettled/i', $this->getResults()->GetTransactionResult->Status)
                ) {
                    return true;
                }
            }
            return false;
        }*/

    /**
     * construct client connection
     * @return Zend_Soap_Client
     */
    protected function getClient()
    {
        if (!$this->_Client) {
            $this->_Client = new Zend_Soap_Client();
            $this->_Client->setSoapVersion(SOAP_1_1);
        }
        return $this->_Client;
    }

    /**
     * Send Zero Dollar Authorize request to Payment gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @return Txp_Express_Model_Base
     */
    public function zeroDollarAuthorize(Varien_Object $payment)
    {
        $this->_place($payment, '0.00', self::TYPE_ACCOUNT_VERIFICATION_CODE);
        return $this;
    }

    /**
     * Send authorize request to Payment gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  decimal $amount
     * @return Txp_Express_Model_Base
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('Express')->__('Invalid amount for authorization.'));
        }
        $this->_initCardsStorage($payment);
        $this->_place($payment, $amount, self::TYPE_AUTH_CODE);
        $payment->setSkipTransactionCreation(true);
        return $this;
    }

    /**
     * Send capture request to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @return Txp_Express_Model_Base
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('Express')->__('Invalid amount for capture.'));
        }
        $this->_initCardsStorage($payment);
        $payment->getOrder()->setSataus(Mage_Sales_Model_Order::STATE_PROCESSING);
        if ($this->_isPreauthorizeCapture($payment)) {
            $this->_place($payment, $amount, self::TYPE_SETTLE_CODE);
        } else {
            $this->_place($payment, $amount, self::TYPE_AUTH_CAPTURE_CODE);
        }
        $payment->setSkipTransactionCreation(true);
        return $this;
    }

    /**
     * Void the payment through gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @return Txp_Express_Model_Base
     * TODO: NOT Complete
     */
    /*    public function void(Varien_Object $payment)
        {
            $cardsStorage = $this->getCardsStorage($payment);
            $this->_place($payment, '0.00', self::TYPE_VOID_CODE);
            $payment->setSkipTransactionCreation(true);
            return $this;
        }*/

    /**
     * Cancel the payment through gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @return Mage_Paygate_Model_Authorizenet
     * TODO: NOT Complete
     */
    /*    public function cancel(Varien_Object $payment)
        {
            return $this->void($payment);
        }*/

    /**
     * Refund the amount with transaction id
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @return Txp_Express_Model_Base
     * @throws Mage_Core_Exception
     * TODO: NOT Complete
     */
    /*    public function refund(Varien_Object $payment, $requestedAmount)
        {
            $cardsStorage = $this->getCardsStorage($payment);
            $this->_place($payment, $requestedAmount, self::TYPE_CREDIT_RETURN_CODE);
            $payment->setSkipTransactionCreation(true);
            return $this;
        }*/

    protected function TransactionZeroAuthorize(Varien_Object $payment, $requestType = null)
    {
        $request = null;
        if (!is_null($requestType)) {
            $request = array(
                'merc' => $this->merchant(),
                'tranCode' => $requestType,
                'Card' => array(
                    'pan' => $payment->getCcNumber(),
                    'xprDt' => substr($payment->getCcExpYear(), 2) . str_pad($payment->getCcExpMonth(), 2, "0", STR_PAD_LEFT),
                    //Optional Fields
                    'sec' => $payment->getCcCid(),
                )
            );
            if (!empty($order)) {
                $billing = $order->getBillingAddress();
		        if ($billing->getCountry() == 'US') {
                    		$contact = array(
                        	'Contact' => array(
                           	 //Required for AVS
                            	'addrLn1' => $billing->getStreet(1),
                            	'zipCode' => substr($billing->getPostcode(),0,5),
                        )
                    );
                }
            }
            $request = array_merge($request, $contact);
        }
        return $request;
    }

    protected function AuthorizeCaptureCreditTransactionRequest(Varien_Object $payment, $amount, $requestType = null)
    {
        $request = null;
        $order = $payment->getOrder();
        if (!is_null($requestType)) {
            $request = array(
                'merc' => $this->merchant(),
                'tranCode' => $requestType,
                'reqAmt' => $this->convertCurrency($payment->getOrder()->getOrderCurrency(), $amount),
                'card' => array(
                    'pan' => $payment->getCcNumber(),
                    'xprDt' => substr($payment->getCcExpYear(), 2) . str_pad($payment->getCcExpMonth(), 2, "0", STR_PAD_LEFT),
                    //Optional Fields
                    'sec' => $payment->getCcCid(),
                ),
                'indCode' => 2
            );
            if (!empty($order)) {
                $billing = $order->getBillingAddress();
		        if ($billing->getCountry() == 'US') {
                        $contact = array(
                            'contact' => array(
                                //Required for AVS
                                'addrLn1' => $billing->getStreet(1),
                                'zipCode' => substr($billing->getPostcode(),0,5),
                                //Optional
                                'fullName' => substr($billing->getFirstname() . ' ' . $billing->getLastname(), 0, 61),
                                'city' => $billing->getCity(),
                                'state' => $billing->getRegionCode(),
                                'ctry' => $billing->getCountry(),
                                'email' => $order->getCustomerEmail()
                            ),
                            'authReq' => array(
                                'ordNr' => $order->getRealOrderId()
                            )
                        );
                }
                $request = array_merge($request, $contact);
            }
        }
        return $request;
    }

    protected function VoidTransactionRequest(Varien_Object $payment, $requestType = null)
    {
        if ($requestType) {
            return array(
                'merc' => $this->merchant(),
                'tranCode' => $requestType,
                'origTranData' => array(
                    'tranNr' => $payment->getLastTransId()
                )
            );
        }
    }

    protected function RefundTransactionRequest(Varien_Object $payment, $amount, $requestType = null)
    {
        if ($requestType) {
            return array(
                'merc' => $this->merchant(),
                'tranCode' => $requestType,
                'reqAmt' => $this->convertCurrency($payment->getOrder()->getOrderCurrency(), $amount),
                'origTranData' => array(
                    'tranNr' => $payment->getLastTransId()
                )
            );
        }
    }

    protected function SettleTransactionRequest(Varien_Object $payment, $amount, $requestType = null)
    {
        if ($requestType) {
            return array(
                'merc' => $this->merchant(),
                'tranCode' => $requestType,
                'reqAmt' => $this->convertCurrency($payment->getOrder()->getOrderCurrency(), $amount),
                'origTranData' => array(
                    'tranNr' => $payment->getLastTransId()
                )
            );
        }
    }

    protected function getTransaction(Varien_Object $payment)
    {
        return array(
            'objTransactionDetailRequest' => array(
                'Credential' => array(
                    'MerchantInfo' => array(
                        'MerchantID' => Mage::getStoreConfig('payment/TxpBase/TXPAccountNumber'),
                        'RegistrationKey' => Mage::getStoreConfig('payment/TxpBase/TXPAccountPassword')
                    )
                ),
                'tranNr' => $payment->getLastTransId()
            )
        );
    }

    protected function getRequest()
    {
        return $this->_Request;
    }

    protected function setRequest(Varien_Object $payment, $amount, $requestType = null)
    {
        if (!is_null($requestType)) {
            switch ($requestType) {
                case self::TYPE_ACCOUNT_VERIFICATION_CODE:
                    $request = $this->TransactionZeroAuthorize($payment, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::TYPE_AUTH_CODE:
                    $this->getTransactionType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
                    $request = $this->AuthorizeCaptureCreditTransactionRequest($payment, $amount, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::TYPE_AUTH_CAPTURE_CODE:
                    $this->getTransactionType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
                    $request = $this->AuthorizeCaptureCreditTransactionRequest($payment, $amount, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::TYPE_SETTLE_CODE:
                    $this->getTransactionType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
                    $request = $this->SettleTransactionRequest($payment, $amount, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::TYPE_CREDIT_RETURN_CODE:
                    $this->getTransactionType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND);
                    $request = $this->RefundTransactionRequest($payment, $amount, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::TYPE_VOID_CODE:
                    $this->getTransactionType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
                    $request = $this->VoidTransactionRequest($payment, $requestType);
                    $this->getWsdlUrl(self::ISSERVICE);
                    break;
                case self::E_REPORT_GET_TRANACTION:
                    $this->getTransactionType('TransactionReport');
                    $request = $this->getTransaction($payment);
                    $this->getWsdlUrl(self::ISREPORT);
                    break;
            }
        }
        $this->_Request = $request;
    }

    /**
     * @set Client Response
     */
    protected function setResults($results)
    {
        $this->_Response = $results;
    }

    /**
     * @return Client Response
     */
    protected function getResults()
    {
        return $this->_Response;
    }

    /**
     * @return Transaction Type for the order
     */
    protected function getTransactionType($newTransactionType = null)
    {
        if ($newTransactionType) {
            $this->_TransactionType = $newTransactionType;
        }
        return $this->_TransactionType;
    }

    /**
     * Send request with new payment to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @param string $requestType
     * @return Txp_Express_Model_Express
     * @throws Mage_Core_Exception
     */
    protected function _place($payment, $amount, $requestType = null)
    {
        $this->getClient();
        $payment->setAmount($amount);
        $payment->setTransType($requestType);
        $this->setRequest($payment, $amount, $requestType);

        if (!$this->getRequest()) {
            Mage::throwException($this->_wrapGatewayError('Could not create request, Please try again.'));
        }

        $this->getClient()->setWsdl($this->getWsdlUrl());


        try {
            $this->setResults($this->getClient()->SendTran($this->getRequest()));
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        if (in_array($this->getResults()->rspCode, $this->_getHelper()->approvalCode())) {
            $this->getCardsStorage($payment)->flushCards();
            $card = $this->_registerCard($this->getResults(), $payment);

            $this->_addTransaction(
                $payment,
                $card->getLastTransId(),
                $this->getTransactionType(),
                array('is_transaction_closed' => 0),
                array($this->_realTransactionIdKey => $card->getLastTransId()),
                Mage::helper('Express')->getTransactionMessage(
                    $payment, $requestType, $card->getLastTransId(), $card, $amount
                )
            );

            switch ($requestType) {
                case self::TYPE_AUTH_CODE:
                    $payment->getOrder()->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                    $payment->setCcStatus(self::STATUS_APPROVED);
                    break;
                case self::TYPE_AUTH_CAPTURE_CODE:
                    $payment->getOrder()->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
                    $payment->setCcStatus(self::STATUS_SUCCESS);
                    $card->setCapturedAmount($card->getProcessedAmount());
                    $this->getCardsStorage($payment)->updateCard($card);
                    break;
            }

            $payment->setLastTransId($this->getResults()->tranData->tranNr);
            $payment->save();
        } else {
            $message = $this->_getHelper()->getExtendedTransactionMessage(
                $payment,
                $payment->getTransType(),
                $this->getResults()->tranData->tranNr,
                $payment,
                $amount,
                true,
                $this->getResults()->rspCode,
                $this->getResults()->extRspCode);
            $this->_getSession()->addError($message);
            Mage::throwException($message);
        }
        return $this;
    }

    /**
     * Return true if there are authorized transactions
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    protected function _isPreauthorizeCapture($payment)
    {
        if ($this->getCardsStorage()->getCardsCount() <= 0) {
            return false;
        }
        foreach ($this->getCardsStorage()->getCards() as $card) {
            $lastTransaction = $payment->getTransaction($card->getLastTransId());
            if (!$lastTransaction
                || $lastTransaction->getTxnType() != Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * Get Current Currency instance, compare current currency to Allowed Currency Code.
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return string $amount
     */
    public function convertCurrency($currency, $amount)
    {
        if (!Mage::helper('Express')->canUseForCurrency($currency->getCurrencyCode())) {
            $amount = Mage::helper('directory')->currencyConvert($amount, $currency->getCurrencyCode(), $this->_allowCurrencyCode);
        }
        return Mage::helper('Express')->formatAmount($amount);
    }

    /**
     * Init cards storage model
     *
     * @param Mage_Payment_Model_Info $payment
     */
    protected function _initCardsStorage($payment)
    {
        $this->_cardsStorage = Mage::getModel('Express/base_cards')->setPayment($payment);
    }

    /**
     * Return cards storage model
     *
     * @param Mage_Payment_Model_Info $payment
     * @return Txp_Express_Model_Base_Cards
     */
    public function getCardsStorage($payment = null)
    {
        if (is_null($payment)) {
            $payment = $this->getInfoInstance();
        }
        if (is_null($this->_cardsStorage)) {
            $this->_initCardsStorage($payment);
        }
        return $this->_cardsStorage;
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote');
        } else {
            return Mage::getSingleton('checkout/session');
        }
    }

    /**
     * Gateway response wrapper
     *
     * @param string $text
     * @return string
     */
    protected function _wrapGatewayError($text)
    {
        return Mage::helper('Express')->__('Gateway error: %s', $text);
    }


    /**
     * TODO: Use report to pull last Transaction data.
     */
    protected function _eReport($payment)
    {
        $this->getClient();
        $this->setRequest($payment, '0.00', self::E_REPORT_GET_TRANACTION);

        if (!$this->getRequest()) {
            Mage::throwException($this->_wrapGatewayError('Could not create request, Please try again.'));
        }
        $this->getClient()->setWsdl($this->getWsdlUrl());
        try {
            $this->setResults($this->getClient()->GetTransaction($this->getRequest()));
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * Sets the card`s data into additional information of payment model
     *
     * @param TransAction Express Soap $client
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Varien_Object
     */
    protected function _registerCard($results, Mage_Sales_Model_Order_Payment $payment)
    {
        $cardsStorage = $this->getCardsStorage($payment);
        $card = $cardsStorage->registerCard();
        $card->setRequestedAmount($payment->getAmount())
            ->setLastTransId($results->tranData->tranNr)
            ->setProcessedAmount(number_format((intval($results->tranData->amt) / 100), 2, '.', ''))
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcLast4($payment->getCcLast4())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
            ->setPostDate(Mage::getModel('core/date')->gmtDate("Y-m-d", strtotime('Today')));
        $cardsStorage->updateCard($card);
        $this->_clearAssignedData($payment);
        return $card;
    }

    /**
     * Reset assigned data in payment info model
     *
     * @param Mage_Payment_Model_Info
     * @return Mage_Paygate_Model_Authorizenet
     */
    private function _clearAssignedData($payment)
    {
        $payment
            //->setCcType(null)
            // ->setCcOwner(null)
            // ->setCcLast4(null)
            ->setCcNumber(null)
            ->setCcCid(null)
            // ->setCcExpMonth(null)
            // ->setCcExpYear(null)
            ->setCcSsIssue(null)
            ->setCcSsStartMonth(null)
            ->setCcSsStartYear(null);
        return $this;
    }

    /**
     * Add payment transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param string $transactionId
     * @param string $transactionType
     * @param array $transactionDetails
     * @param array $transactionAdditionalInfo
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    protected function _addTransaction(
        Mage_Sales_Model_Order_Payment $payment,
        $transactionId,
        $transactionType,
        array $transactionDetails = array(),
        array $transactionAdditionalInfo = array(),
        $message = false
    )
    {
        $payment->setTransactionId($transactionId);
        $payment->resetTransactionAdditionalInfo();
        foreach ($transactionDetails as $key => $value) {
            $payment->setData($key, $value);
        }
        foreach ($transactionAdditionalInfo as $key => $value) {
            $payment->setTransactionAdditionalInfo($key, $value);
        }
        $transaction = $payment->addTransaction($transactionType, null, false, $message);
        foreach ($transactionDetails as $key => $value) {
            $payment->unsetData($key);
        }
        $payment->unsLastTransId();

        /**
         * It for self using
         */
        $transaction->setMessage($message);

        return $transaction;
    }

    /**
     * If gateway actions are locked return true
     *
     * @param  Mage_Payment_Model_Info $payment
     * @return bool
     */
    protected function _isGatewayActionsLocked($payment)
    {
        return $payment->getAdditionalInformation($this->_isGatewayActionsLockedKey);
    }
}
