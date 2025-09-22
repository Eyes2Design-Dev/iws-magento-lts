<?php

class TM_Attributepages_Block_Abstract extends Mage_Core_Block_Template
{
    public function getTitle()
    {
        return $this->_getConfigurableParam('title');
    }

    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentPage()
    {
        if (!$this->hasData('current_page')) {
            if ($identifier = $this->getData('identifier')) {
                $currentPage = Mage::getModel('attributepages/entity')->load($identifier);
                if ($currentPage->getId()) {
                    $this->setData('current_page', $currentPage);
                }
            } else {
                $this->setData('current_page', Mage::registry('attributepages_current_page'));
            }
        }
        return $this->getData('current_page');
    }

    protected function _getConfigurableParam($key)
    {
        $data = $this->_getData($key);
        if (null === $data) {
            $currentPage = $this->getCurrentPage();
            if ($currentPage) {
                $this->setData($key, $currentPage->getData($key));
            }
        }
        return $this->_getData($key);
    }
}
