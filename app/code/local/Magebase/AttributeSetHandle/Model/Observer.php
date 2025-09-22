<?php
class Magebase_AttributeSetHandle_Model_Observer
{
    /**
     * Converts attribute set name of current product to nice name ([a-z0-9_]+).
     * Adds layout handle PRODUCT_ATTRIBUTE_SET_<attribute_set_nicename> after
     * PRODUCT_TYPE_<product_type_id> handle
     *
     * Event: controller_action_layout_load_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function addAttributeSetHandle(Varien_Event_Observer $observer)
    {
        $product = Mage::registry('current_product');
 
        /**
         * Return if it is not product page
         */
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return;
        }
 
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId());
        /**
         * Convert attribute set name to alphanumeric + underscore string
         */
        $niceName = str_replace('-', '_', $product->formatUrlKey($attributeSet->getAttributeSetName()));
 
        /* @var $update Mage_Core_Model_Layout_Update */
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $handles = $update->getHandles(); // Store all handles in a variable
        $update->resetHandles(); // Remove all handles
 
        /**
         * Rearrange layout handles to ensure PRODUCT_<product_id>
         * handle is added last
         */
        foreach ($handles as $handle) {
            $update->addHandle($handle);
            if ($handle == 'PRODUCT_TYPE_' . $product->getTypeId()) {
                $update->addHandle('PRODUCT_ATTRIBUTE_SET_' . $niceName);
            }
        }
    }
}