<?php

class TM_Attributepages_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function canUseLayeredNavigation()
    {
        if (!Mage::getStoreConfigFlag('attributepages/product_list/use_layered_navigation')) {
            return false;
        }

        // for now we didn't test compatibility with third party extensions,
        // so just disable layer if it's not the magento standard navigation
        $layer = Mage::getModel('catalog/layer');
        if (get_class($layer) !== 'Mage_Catalog_Model_Layer') {
            return false;
        }

        $filter = Mage::getModel('catalog/layer_filter_attribute');
        if (get_class($filter) !== 'Mage_Catalog_Model_Layer_Filter_Attribute') {
            return false;
        }

        $helper = Mage::helper('core');
        $unsupportedModules = array('TM_AjaxLayeredNavigation');
        foreach ($unsupportedModules as $moduleName) {
            if ($helper->isModuleOutputEnabled($moduleName)) {
                return false;
            }
        }
        return true;
    }
}
