<?php
class ModernizedMedia_PurchaseWizard_AjaxController extends Mage_Core_Controller_Front_Action {

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function indexAction() {
        $this->displayData(array("bob"=>32));
    }

    public function getProductsAction(){
        $categoryID = (int) $this->getRequest()->getParam('category');
        $type =  filter_var($this->getRequest()->getParam('bundleType'), FILTER_SANITIZE_STRING);

        $helper = Mage::helper('purchasewizard/Wizard');

        $typeID = 0;
        if ($type=='pack'){
            $typeID = $helper::PACK;
        } else if ($type=='kit'){
            $typeID = $helper::KIT;
        } else {
            // log error
        }

        $data = array();
        $data = $helper->getProducts(array("typeID"=>$typeID,"categoryID"=>$categoryID));

        $this->displayData($data);
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load((int) $productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }


    public function addAction() {
        if (!$this->_validateFormKey()) {
            $this->displayData(['success' => false, 'message' => 'Form Key is invalid.']);
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getPost();
        try {
            if (isset($params['post_data'])) {
                foreach ($params['post_data'] as $item) {
                    $productId = $item['item_id'];
                    $qty = $item['qty'];
                    if ($item['qty'] == 0) { continue; }
                    if (isset($qty)) {
                        $filter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                        );
                        $qty = $filter->filter($qty);
                    }
                    $product = $this->_initProduct($productId);
                    if (!$product) { continue; }
                    $cart->addProduct($product, $qty);
                }
                $cart->save();
                $this->_getSession()->setCartWasUpdated(true);
                $this->displayData(['success' => true]);
            } else {
                $this->displayData(['success' => false, 'message' => 'Cannot retrieve Post Data.']);
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->displayData(['success' => false, 'message' => 'Cannot add the items to shopping cart.']);
        }
    }

    private function displayData($data){
        $this->getResponse()->setHeader("Content-Type","application/json");
        $this->getResponse()->setBody(json_encode($data));
    }
}