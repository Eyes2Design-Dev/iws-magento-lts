<?php

class TM_SoldTogether_Block_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    public function getCacheKeyInfo()
    {
        $productId = 0;
        if ($product = Mage::registry('product')) {
            $productId = $product->getId();
        }
        return array(
            $this->_cachePrefix,
            Mage::app()->getStore()->getId(),
            Mage::app()->getStore()->getCurrentCurrencyCode(),
            Mage::getDesign()->getPackageName(),
            $this->getTemplate(),
            $this->getProductsCount(),
            $this->getColumnsCount(),
            $this->getNameInLayout(),
            $productId
        );
    }

    /**
     * Retrieve product final price in current currency
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  boolean $includingTax
     * @return float
     */
    public function getProductFinalPrice($product, $includingTax = false)
    {
        $basePrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $includingTax);
        return round(Mage::helper('core')->currency($basePrice, false, false), 2);
    }

    protected function _toHtml()
    {
        if (!$this->getProductCollection()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getProductsCount()
    {
        if (!isset($this->_data['products_count'])) {
            $this->_data['products_count'] =
                Mage::getStoreConfig("soldtogether/{$this->_configGroup}/productscount");
        }
        return $this->_data['products_count'];
    }

    public function getColumnsCount()
    {
        if (!isset($this->_data['columns_count'])) {
            $this->_data['columns_count'] =
                Mage::getStoreConfig("soldtogether/{$this->_configGroup}/columns");
        }
        return $this->_data['columns_count'];
    }
}