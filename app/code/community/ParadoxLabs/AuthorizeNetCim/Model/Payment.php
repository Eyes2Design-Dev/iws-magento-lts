<?php
/**
 * Authorize.Net CIM - Payment model. "The brains."
 *
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Having a problem with the plugin?
 * Not sure what something means?
 * Need custom development?
 * Give us a call!
 *
 * @category	ParadoxLabs
 * @package		ParadoxLabs_AuthorizeNetCim
 * @author		Ryan Hoerr <ryan@paradoxlabs.com>
 */


class ParadoxLabs_AuthorizeNetCim_Model_Payment extends Mage_Payment_Model_Method_Cc
{
	protected $_formBlockType			= 'authnetcim/form';
    protected $_infoBlockType			= 'authnetcim/info';
	protected $_code					= 'authnetcim';
	protected $_debug					= true;
	protected $_admin					= false;
	
	// Can-dos
	protected $_isGateway				= false;
	protected $_canAuthorize			= true;
	protected $_canCapture				= true;
	protected $_canCapturePartial		= true;
	protected $_canRefund				= true;
	protected $_canRefundInvoicePartial = true;
	protected $_canVoid					= true;
	protected $_canUseInternal			= true;
	protected $_canUseCheckout			= true;
	protected $_canUseForMultishipping	= true;
	protected $_canSaveCc				= false; // Don't want Magento saving the card itself.
	protected $_canReviewPayment		= false;
	protected $_canCancelInvoice		= true;
	protected $_canManageRecurringProfiles = false;
	protected $_canFetchTransactionInfo = true;
	
	protected $cim						= null;
	protected $_invoice					= null;
	protected $_customer				= null;
	protected $_storeId					= 0;
	
	/**
	 * Initialize Authorize.net CIM class and related flags.
	 */
	public function __construct() {
		if( Mage::app()->getStore()->isAdmin() ) {
			$this->_admin = true;
		}
		
		if( $this->_admin && Mage::registry('current_order') != false ) {
			$this->setStore( Mage::registry('current_order')->getStoreId() );
		}
		elseif( $this->_admin && Mage::registry('current_invoice') != false ) {
			$this->setStore( Mage::registry('current_invoice')->getStoreId() );
		}
		elseif( $this->_admin && Mage::registry('current_creditmemo') != false ) {
			$this->setStore( Mage::registry('current_creditmemo')->getStoreId() );
		}
		elseif( $this->_admin && Mage::registry('current_customer') != false ) {
			$this->setStore( Mage::registry('current_customer')->getStoreId() );
		}
		elseif( $this->_admin && Mage::getSingleton('adminhtml/session_quote')->getStoreId() > 0 ) {
			$this->setStore( Mage::getSingleton('adminhtml/session_quote')->getStoreId() );
		}
		else {
			$this->setStore( Mage::app()->getStore()->getId() );
		}
		
		return $this;
	}
	
	/**
	 * Set the payment config scope and reinitialize the API
	 */
	public function setStore( $id ) {
		$this->_storeId = $id;
		
		$this->initializeApi( true );
		
		return $this;
	}
	
	/**
	 * Set the customer to use for payment/card operations.
	 */
	public function setCustomer( $customer ) {
		$this->_customer = $customer;
		
		return $this;
	}
	
	/**
	 * Fetch a setting for the current store scope.
	 */
    public function getConfigData( $field, $storeId=null ) {
        if( is_null( $storeId ) ) {
            $storeId = $this->_storeId;
        }

        return Mage::getStoreConfig( 'payment/' . $this->getCode() . '/' . $field, $storeId );
    }
    
    /**
     * Get the current customer; fetch from session if necessary.
     */
	public function getCustomer() {
		if( isset( $this->_customer ) ) {
			$customer = $this->_customer;
		}
		elseif( $this->_admin ) {
			$customer = Mage::getModel('customer/customer')->load( Mage::getSingleton('adminhtml/session_quote')->getCustomerId() );
		}
		else {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		
		$this->setCustomer( $customer );
		
		return $customer;
	}
	
	/**
	 * Initialize the API gateway class. 'force' will reinitialize
	 * in the current config scope.
	 */
	protected function initializeApi( $force=false ) {
		if( $force === true ) {
			$this->cim = null;
		}
		
		if( is_null( $this->cim ) ) {
			$this->_debug = $this->getConfigData('debug');
			
			$this->cim = Mage::getModel('authnetcim/api')->init(	$this->getConfigData('login'),
																	$this->getConfigData('trans_key'),
																	$this->getConfigData('test'),
																	$this->getConfigData('validation_mode') );
		}
		
		return $this;
	}
	
	/**
	 * Update the CC info during the checkout process.
	 */
	public function assignData( $data ) {
		parent::assignData( $data );
		
		$post = Mage::app()->getRequest()->getParam('payment');
		
		if( !empty( $post['payment_id'] ) ) {
			$card = $this->getPaymentInfoById( $post['payment_id'], false );
			
			if( $card && $card['label'] != '' ) {
				$this->getInfoInstance()->setAdditionalInformation( 'method', ( strlen( $card['label'] ) > 9 ? 'ECHECK' : 'CC' ) )
										->setCcLast4( substr( $card['label'], -4 ) )
										->setCcType( '' );
			}
		}
		
		return $this;
	}
	
	/**
	 * Validate the transaction inputs.
	 */
	public function validate() {
		if( $this->_debug ) Mage::log('validate()', null, 'authnetcim.log');
		
		$post = Mage::app()->getRequest()->getParam('payment');
		
		if( empty($post['payment_id']) || !empty($post['cc_number']) ) {
			try {
				return parent::validate();
			}
			catch(Exception $e) {
				return $e->getMessage();
			}
		}
		else {
			return true;
		}
	}

	/**
	 * Authorize a transaction
	 */
	public function authorize(Varien_Object $payment, $amount) {
		if( $this->_debug ) Mage::log('authorize()', null, 'authnetcim.log');
		
		$post = Mage::app()->getRequest()->getParam('payment');
		$payment_id = 0;
		
		if( !empty($post['payment_id']) && empty($post['cc_number']) ) {
			$profile_id = $this->getProfileId( $this->getCustomer() );
			$payment_id = intval( $post['payment_id'] );
			
			$payment->getOrder()->setExtCustomerId( $profile_id.':'.$payment_id )->save();
		}
		
		return $this->bill( $payment, $amount, 'profileTransAuthOnly' );
	}

	/**
	 * Capture a transaction [authorize if necessary]
	 */
	public function capture(Varien_Object $payment, $amount) {
		if( $this->_debug ) Mage::log('capture()', null, 'authnetcim.log');
	
		$post = Mage::app()->getRequest()->getParam('payment');
		$payment_id = 0;
		
		if( !empty($post['payment_id']) ) {
			$profile_id = $this->getProfileId( $this->getCustomer() );
			$payment_id = intval( $post['payment_id'] );
			
			$payment->getOrder()->setExtCustomerId( $profile_id.':'.$payment_id )->save();
		}
		
		$trans_id = explode( ':', $payment->getOrder()->getExtOrderId() );
		$type     = !empty($trans_id[1]) ? 'profileTransPriorAuthCapture' : 'profileTransAuthCapture';
		
		// Handle partial-invoice with expired auth
		if( $type == 'profileTransPriorAuthCapture' && $payment->getOrder()->getTotalPaid() > 0 ) {
			$type = 'profileTransCaptureOnly';
		}
		
		// Grab the invoice in case partial invoicing
		$invoice = Mage::registry('current_invoice');
		if( !is_null( $invoice ) ) {
			$this->_invoice = $invoice;
		}
		
		return $this->bill( $payment, $amount, $type );
	}

	/**
	 * Refund a transaction
	 */
	public function refund(Varien_Object $payment, $amount) {
		if( $this->_debug ) Mage::log('refund()', null, 'authnetcim.log');
		
		// Grab the invoice in case partial invoicing
		$creditmemo = Mage::registry('current_creditmemo');
		if( !is_null( $creditmemo ) ) {
			$this->_invoice = $creditmemo->getInvoice();
		}
		
		return $this->bill( $payment, $amount, 'profileTransRefund' );
	}

	/**
	 * Void a payment
	 */
	public function void(Varien_Object $payment) {
		if( $this->_debug ) Mage::log('void()', null, 'authnetcim.log');
		
		try {
			$profile_id  = explode( ':', $payment->getOrder()->getExtCustomerId() );
			$trans_id    = explode( ':', $payment->getOrder()->getExtOrderId() );
			
			$this->cim->setParameter( 'customerProfileId', $profile_id[0] );
			$this->cim->setParameter( 'transId', $trans_id[0] );
			$this->cim->voidCustomerProfileTransaction();
			
			$this->checkCimErrors();
			
			$trans_id = $this->cim->getTransactionID() ? $this->cim->getTransactionID() : $trans_id[0].'-2';
			
			Mage::log( $this->cim->getDirectResponse(), null, 'authnetcim.log', true );
			
			$payment->getOrder()->setExtOrderId($trans_id);
			
			$payment->setTransactionId($trans_id)
					->setIsTransactionClosed(1)
					->setShouldCloseParentTransaction(1)
					->save();
		}
		catch (AuthnetCIMException $e) {
			Mage::log( $e->getMessage(), null, 'authnetcim.log', true );
		}
		
		return $this;
	}
	
	/**
	 * Cancel a payment
	 */
	public function cancel(Varien_Object $payment) {
		if( $this->_debug ) Mage::log('cancel()', null, 'authnetcim.log');
		
		return $this->void($payment);
	}
	
	/**
	 * Fetch transaction info -- for use with fraud detection
	 */
	public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId) {
		if( $this->_debug ) Mage::log('fetchTransactionInfo('.$transactionId.')', null, 'authnetcim.log');
		
		$transaction = $payment->getTransaction($transactionId);
		
		if (!$transaction->getAdditionalInformation('is_transaction_fraud')) {
			return parent::fetchTransactionInfo($payment, $transactionId);
		}
		
		$this->cim->clearParameters();
		$this->cim->setParameter( 'transaction_id', $transactionId );
		$this->cim->getTransactionDetails();
		
		$this->checkCimErrors( true );
		
		Mage::log( json_encode( (array)$this->cim->raw->transaction ), null, 'authnetcim.log' );
		
		if( (int)$this->cim->raw->transaction->responseCode == 1 ) { // Transaction approved
			$transaction->setAdditionalInformation( 'is_transaction_fraud', false );
			$payment->setIsTransactionApproved( true );
		}
		elseif( (int)$this->cim->raw->transaction->getResponseReasonCode == 254 ) { // Transaction pending review -> denied
			$payment->setIsTransactionDenied( true );
		}
		
		return parent::fetchTransactionInfo($payment, $transactionId);
	}
	
	/**
	 * Payment method available? Yes.
	 */
	public function isAvailable($quote=null) {
		return (bool)($this->getConfigData('active'));
	}
	
	/**
	 * Fetch current customer's payment profiles and masked
	 * card number if available.
	 */
	public function getPaymentInfo( $profile_id=0 ) {
		if( $this->_debug ) Mage::log('getPaymentInfo('.$profile_id.')', null, 'authnetcim.log');

		$_customer = $this->getCustomer();

		if( empty($profile_id) ) {
			$profile_id = $this->getProfileId($_customer);
		}
		
		if( !empty($profile_id) ) {
			// Fetch
			$this->cim->setParameter( 'customerProfileId', $profile_id );
			$this->cim->getCustomerProfile();

			$this->checkCimErrors();

			if( $this->cim->getCode() == 'E00040' ) {
				$profile_id = $this->createCustomerProfile( $_customer );
				return $this->getPaymentInfo( $profile_id );
			}
			
			$info = array(
				'cc'	=> array(),
				'bank'	=> array()
			);
			
			// Format and return
			if( count( $this->cim->raw->profile->paymentProfiles ) ) {
				foreach( $this->cim->raw->profile->paymentProfiles as $payment ) {
					// bank or CC?
					if( isset( $payment->payment->bankAccount ) ) {
						$info['bank'][] = array(
							'payment_id'	=> (string) $payment->customerPaymentProfileId,
							'label'			=> $payment->payment->bankAccount->bankName . ' x-' . substr( $payment->payment->bankAccount->accountNumber, -4 )
						);
					}
					else {
						$info['cc'][] = array(
							'payment_id'	=> (string) $payment->customerPaymentProfileId,
							'label'			=> 'XXXX-' . substr( $payment->payment->creditCard->cardNumber, -4 )
						);
					}
				}
			}
			
			return $info;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Fetch a payment profile by ID.
	 */
	public function getPaymentInfoById( $payment_id, $raw=false, $profile_id=0 ) {
		if( $this->_debug ) Mage::log('getPaymentInfoById('.$payment_id.')', null, 'authnetcim.log');

		if( intval( $profile_id ) < 1 ) {
			$profile_id = $this->getProfileId( $this->getCustomer() );
		}
		
		if( !empty($profile_id) && !empty($payment_id) ) {
			// Fetch
			$this->cim->clearParameters();
			$this->cim->setParameter( 'customerProfileId', $profile_id );
			$this->cim->setParameter( 'customerPaymentProfileId', $payment_id );
			$this->cim->getCustomerPaymentProfile();

			$this->checkCimErrors();

			if( $this->cim->getCode() == 'E00040' ) {
				Mage::log( 'CIM: '.$this->cim->responses, null, 'authnetcim.log', true );
				return false;
			}
			
			// Format and return
			if( $raw ) {
				return $this->cim->raw->paymentProfile;
			}
			
			$payment = $this->cim->raw->paymentProfile;
			if( isset( $payment->payment->bankAccount ) ) {
				return array(
					'payment_id'	=> (string) $payment->customerPaymentProfileId,
					'label'			=> $payment->payment->bankAccount->bankName . ' x-' . substr( $payment->payment->bankAccount->accountNumber, -4 )
				);
			}
			else {
				return array(
					'payment_id'	=> (string) $payment->customerPaymentProfileId,
					'label'			=> 'XXXX-' . substr( $payment->payment->creditCard->cardNumber, -4 )
				);
			}
		}
		else {
			return new Varien_Object();
		}
	}
	
	/**
	 * Get or create customer profile ID
	 */
	protected function getProfileId($_customer, $payment=null) {
		if( $this->_debug ) Mage::log('getProfileId()', null, 'authnetcim.log');
		
		$profile_id = $_customer->getAuthnetcimProfileId();
		if( intval($profile_id) < 1 ) {
			$profile_id	= $this->createCustomerProfile( $_customer, $payment );
		}
		
		return !empty($profile_id) ? $profile_id : 0;
	}
	
	/**
	 * Generate an Authorize.net CIM customer profile.
	 */
	protected function createCustomerProfile($_customer, $payment=null) {
		if( $this->_debug ) Mage::log('createCustomerProfile()', null, 'authnetcim.log');
		
		try {
			$email 	= $_customer->getEmail();
			$uid 	= $_customer->getEntityId();

			/**
			 * If not logged in, we must be checking out as a guest--try to grab their info.
			 */
			if( empty($email) || $uid < 2 ) {
				$sess = Mage::getSingleton('core/session')->getData();
				
				if( $payment != null && $payment->getQuote() != null ) {
					$email 	= $payment->getQuote()->getCustomerEmail();
					$uid 	= is_numeric($payment->getQuote()->getCustomerId()) ? $payment->getQuote()->getCustomerId() : 0;
				}
				elseif( $payment != null && $payment->getOrder() != null ) {
					$email 	= $payment->getOrder()->getCustomerEmail();
					$uid 	= is_numeric($payment->getOrder()->getCustomerId()) ? $payment->getOrder()->getCustomerId() : 0;
				}
				elseif( isset($sess['visitor_data']) && !empty($sess['visitor_data']['quote_id']) ) {
					$quote 	= Mage::getModel('sales/quote')->load( $sess['visitor_data']['quote_id'] );
					
					$email 	= $quote->getCustomerEmail();
					$uid 	= is_numeric($quote->getCustomerId()) ? $quote->getCustomerId() : 0;
				}
				
				$_customer->setEmail( $email );
				$_customer->setEntityId( $uid );
			}
			
			/**
			 * Failsafe: We must have some email to go through here. The data might not
			 * actually be available.
			 */
			if( empty($email) ) {
				Mage::log("No customer email found; can't create a CIM profile.", null, 'authnetcim.log');
				return false;
			}
			
			/**
			 * If we have no customer ID, default to something session-unique so we don't
			 * risk any collisions.
			 */
			if( $uid < 1 ) {
				$uid = md5( Mage::getSingleton("core/session")->getEncryptedSessionId() . ':' . $_SERVER['REMOTE_ADDR'] );
			}
			
			$this->cim->clearParameters();
			$this->cim->setParameter( 'email', $email );
			$this->cim->setParameter( 'merchantCustomerId', $uid );
			$this->cim->createCustomerProfile();
			
			$profile_id = $this->cim->getProfileID();
			
			$this->checkCimErrors();
			
			/**
			 * Handle 'duplicate' errors
			 */
			if( strpos($this->cim->getResponse(), 'duplicate') !== false ) {
				$profile_id = preg_replace( '/[^0-9]/', '', $this->cim->getResponse() );
			}
			
			
			$_customer->setAuthnetcimProfileId( $profile_id );
			if( $_customer->getData('entity_id') > 0 ) {
				$_customer->save();
			}
			
			return $profile_id;
		}
		catch (AuthnetCIMException $e) {
			Mage::log( $e->getMessage(), null, 'authnetcim.log', true );
			return false;
		}
	}
	
	/**
	 * Fetch a Hosted page token from Authorize.Net.
	 */
	public function getHostedToken( $profile_id=0 ) {
		if( $this->_debug ) Mage::log('getHostedToken('.$profile_id.')', null, 'authnetcim.log');
		
		$this->initializeApi();
		
		if( intval( $profile_id ) < 1 ) {
			$profile_id = $this->getProfileId( $this->getCustomer() );
		}
		
		if( intval( $profile_id ) > 0 ) {
			$this->cim->clearParameters();
			$this->cim->setParameter( 'customerProfileId', intval( $profile_id ) );
			$this->cim->getHostedProfilePage();
			
			$this->checkCimErrors();
			
			$token = $this->cim->getToken();
		}
		else {
			Mage::log( "Unable to get CIM profile ID; could not request API token.", null, 'authnetcim.log' );
			$token = '';
		}
		
		return $token;
	}
	
	/**
	 * Generate an authorize or capture transaction from existing profiles.
	 */
	protected function bill( $payment, $amount, $type = 'profileTransAuthOnly' ) {
		if( $this->_debug ) Mage::log('bill(), type='.$type, null, 'authnetcim.log');
		
		$this->initializeApi();
		
		try {
			$this->cim->clearParameters();
			
			$trans_id   = explode( ':', $payment->getOrder()->getExtOrderId() );
			$profile 	= explode( ':', $payment->getOrder()->getExtCustomerId() );
			$profile_id = isset( $profile[0] ) ? $profile[0] : 0;
			$payment_id = isset( $profile[1] ) ? $profile[1] : 0;
			
			// Handle transaction ID for partial invoicing
			if( !is_null( $this->_invoice ) && $this->_invoice->getTransactionId() != '' ) {
				$trans_id[0] = $this->_invoice->getTransactionId();
			}
			
			if( empty($profile_id) || empty($payment_id) ) {
				Mage::log( "\n".$this->cim->responses, null, 'authnetcim.log', true );
				Mage::throwException( "Invalid account or payment information. Please try again." );
			}
			
			if( $amount <= 0 ) {
				return $this;
			}
			
			$this->cim->setParameter( 'invoiceNumber', $payment->getOrder()->getIncrementId() );
			$this->cim->setParameter( 'amount', round( $amount, 4 ) );
			
			if( $payment->getOrder()->getBaseTaxAmount() && ( $type == 'profileTransAuthOnly' || $type == 'profileTransAuthCapture' ) ) {
				$this->cim->setParameter( 'taxAmount', round( $payment->getOrder()->getBaseTaxAmount(), 4 ) );
			}
			
			if( $payment->getBaseShippingAmount() ) {
				$this->cim->setParameter( 'shipAmount', round( $payment->getBaseShippingAmount(), 4 ) );
			}
			
			if( !empty($trans_id[1]) ) {
				$this->cim->setParameter( 'approvalCode', $trans_id[1] );
			}
			
			$this->cim->setParameter( 'customerProfileId', $profile_id );
			$this->cim->setParameter( 'customerPaymentProfileId', $payment_id );
			
			// Handle PriorAuth with no transaction ID--never authorized.
			if( empty($trans_id[0]) && $type == 'profileTransPriorAuthCapture' ) {
				$type = 'profileTransAuthCapture';
			}
			
			if( $type == 'profileTransRefund' || $type == 'profileTransPriorAuthCapture' ) {
				$this->cim->setParameter( 'transId', $trans_id[0] );
			}
			
			$this->cim->createCustomerProfileTransaction( $type );
			
			$this->checkCimErrors();
			
			if( $this->cim->isError() || ( $type != 'profileTransRefund' && ( !$this->cim->getTransactionID() || ( $this->cim->getTransactionType() != 'ECHECK' && !$this->cim->getAuthCode() ) ) ) ) {
				Mage::log( "\n".$this->cim->responses, null, 'authnetcim.log', true );
				Mage::throwException( "Authorize.Net CIM Gateway: Transaction failed. ".$this->cim->getResponse() );
			}
			
			// Record transaction result
			$response = $this->getCimResponse();
			$response->setProfileId( (int) $profile_id )
					 ->setPaymentId( (int) $payment_id );
			
			// If we need to, don't save the card
			$response = $this->getCimResponse();
			$response->setProfileId( (int) $profile_id )
					 ->setPaymentId( (int) $payment_id );
			
			$payment->setTransactionId( $this->cim->getTransactionID() )
					->setCcLast4( $this->cim->getCcLast4() )
					->setCcType( $this->cim->getCcType() )
					->setAdditionalInformation( $response->getData() );
			
			if( $type == 'profileTransAuthOnly' ) {
				$payment->setIsTransactionClosed(0);
			}
			else {
				$payment->setIsTransactionClosed(1);
			}
			
			if( !in_array( $type, array( 'profileTransRefund', 'profileTransPriorAuthCapture', 'profileTransCaptureOnly' ) ) ) {
				$payment->getOrder()->setExtOrderId( $this->cim->getTransactionID().':'.$this->cim->getAuthCode() );
				
				if( !$payment->getIsFraudDetected() ) {
					$payment->getOrder()->setState( $this->getConfigData('order_status') )
										->setStatus( $this->getConfigData('order_status') );
				}
			}

			$payment->getOrder()->setExtCustomerId( $profile_id.':'.$payment_id )
								->save();

			Mage::log( $this->cim->getDirectResponse(), null, 'authnetcim.log', true );
		}
		catch (AuthnetCIMException $e) {
			Mage::log( $e->getMessage(), null, 'authnetcim.log', true );
		}
		
		return $this;
	}
	
	/**
	 * Parse Authorize.Net direct response into object
	 */
	protected function getCimResponse() {
		$result = new Varien_Object;
		$r 		= explode( $this->cim->getDelimiter(), str_replace('"','',$this->cim->getDirectResponse()) );
		
		if( count($r) > 0 ) {
			$result->setResponseCode((int)$r[0])
				->setResponseSubcode((int)$r[1])
				->setResponseReasonCode((int)$r[2])
				->setResponseReasonText($r[3])
				->setApprovalCode($r[4])
				->setAvsResultCode($r[5])
				->setTransactionId($r[6])
				->setInvoiceNumber($r[7])
				->setDescription($r[8])
				->setAmount($r[9])
				->setMethod($r[10])
				->setTransactionType($r[11])
				->setCustomerId($r[12])
				->setMd5Hash($r[37])
				->setCardCodeResponseCode($r[38])
				->setCAVVResponseCode( (isset($r[39])) ? $r[39] : null)
				->setAccNumber($r[50])
				->setCardType($r[51])
				->setSplitTenderId($r[52])
				->setRequestedAmount($r[53])
				->setBalanceOnCard($r[54]);
		}
		
		return $result;
	}

	/**
	 * Handle game-over errors
	 */
	public function checkCimErrors( $err=false ) {
		$from = Mage::getStoreConfig('trans_email/ident_general/email');
		$code = $this->cim->getCode();
		
		// Bad login ID / trans key
		if( $code == 'E00007' ) {
			$subj = 'Authorize.Net CIM Payment Module - Invalid API details';
			$body = "Warning: Your Authorize.net CIM API Login ID or Transaction Key appears to be incorrect, or you may be using live credentials with test mode enabled. The payment module is unable to authenticate properly. CIM purchasing will not work properly until this is fixed.";
			mail( $from, $subj, $body, "From: " . $from . "\r\n" );
		}

		// CIM not enabled
		if( $code == 'E00044' ) {
			$subj = 'Authorize.Net CIM Payment Module - CIM not enabled';
			$body = "Warning: CIM is not enabled on your Authorize.net account. CIM purchasing will not work properly until this is fixed.";
			mail( $from, $subj, $body, "From: " . $from . "\r\n" );
		}

		// Generic error
		if( $this->cim->isError() && !empty($code) ) {
			Mage::log('API error: '.$code.': '.$this->cim->getResponse(), null, 'authnetcim.log', true );
			Mage::log( "CIM: ".$this->cim->responses, null, 'authnetcim.log', true );
			
			if( $err ) {
				Mage::throwException( 'Authorize.Net CIM Gateway: '.$this->cim->getResponse() );
			}
		}
	}
}
