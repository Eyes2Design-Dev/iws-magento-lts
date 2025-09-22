<?php

class TM_Attributepages_PageController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $page = Mage::registry('attributepages_current_page');

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default')
            ->addHandle('ATTRIBUTEPAGES_PAGE_' . $page->getId());
        $this->addActionLayoutHandles();

        if ($page->isAttributeBasedPage()) {
            $update->addHandle('attributepages_attribute_page');
        } else {
            $update->addHandle('attributepages_option_page');

            if (Mage::helper('attributepages')->canUseLayeredNavigation()) {
                $update->addHandle('attributepages_option_page_layered');
            } else {
                $update->addHandle('attributepages_option_page_default');
            }
        }

        if ($handle = $page->getRootTemplate()) {
            $layout->helper('page/layout')->applyHandle($handle);
        }

        $this->loadLayoutUpdates();
        $update->addUpdate($page->getLayoutUpdateXml());
        $this->generateLayoutXml()->generateLayoutBlocks();

        if ($root = $layout->getBlock('root')) {
            if ($page->isAttributeBasedPage()) {
                $suffix = '-attribute-page';
            } else {
                $suffix = '-option-page';
            }
            $root->addBodyClass('attributepages-' . $suffix);
            $root->addBodyClass('attributepages-' . $page->getIdentifier());
        }

        if ($breadcrumbs = $layout->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => Mage::helper('cms')->__('Home'),
                'title' => Mage::helper('cms')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ));
            if ($parentPage = Mage::registry('attributepages_parent_page')) {
                $breadcrumbs->addCrumb('parent_page', array(
                    'label' => $parentPage->getTitle(),
                    'title' => $parentPage->getTitle(),
                    'link'  => Mage::getUrl($parentPage->getIdentifier())
                ));
            }
            $breadcrumbs->addCrumb('current_page', array(
                'label' => $page->getTitle(),
                'title' => $page->getTitle()
            ));
        }

        if ($headBlock = $layout->getBlock('head')) {
            if ($title = $page->getTitle()) {
                $headBlock->setTitle($title);
            }
            if ($description = $page->getMetaDescription()) {
                $headBlock->setDescription($description);
            }
            if ($keywords = $page->getMetaKeywords()) {
                $headBlock->setKeywords($keywords);
            }
        }

        if ($page->isOptionBasedPage()) {
            $this->_initCollectionFilters($page);
        }

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    protected function _initCollectionFilters($page)
    {
        $layout = $this->getLayout();
        $layer  = Mage::getSingleton('catalog/layer');
        $productCollection = $layout->getBlock('children_list')->getLoadedProductCollection();

        // filter by category
        $categoryId = (int) $this->getRequest()->getParam('cat', false);
        $category = false;
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($categoryId);

            if (!Mage::helper('catalog/category')->canShow($category)) {
                $category = false;
            }
        }

        if (!$category) {
            $category = $layer->getCurrentCategory();
        }
        /**
         * Hack to call for unset($this->_productLimitationFilters['category_is_anchor']);
         * in Mage/Catalog/Model/Resource/Product/Collection.php::addCategoryFilter
         * to remove cat_index.is_parent filter
         */
        $category->setIsAnchor(1);
        $productCollection->addCategoryFilter($category);

        // remove page attribute from filters
        $layerBlockNames = Mage::getStoreConfig('attributepages/product_list/layer_block_name');
        foreach (explode(',', $layerBlockNames) as $layerBlockName) {
            $layerBlock = $layout->getBlock($layerBlockName);
            if (!$layerBlock) {
                continue;
            }
            $filterableAttributes = $layer->getFilterableAttributes();
            if ($filterableAttributes) {
                /**
                 * Previous hack causes filterable attribute recalculation, so
                 * we need to create dummy blocks for new filters to
                 * prevent error in layer/view.phtml
                 */
                foreach ($filterableAttributes as $attribute) {
                    if (!$layerBlock->getChild($attribute->getAttributeCode() . '_filter')) {
                        $layerBlock->setChild(
                            $attribute->getAttributeCode() . '_filter',
                            $layout->createBlock('core/template')
                        );
                    }
                }
                $filterableAttributes->removeItemByKey($page->getAttribute()->getAttributeId());
            }
            $layerBlock->setData('_filterable_attributes', $filterableAttributes);
        }

        /**
         * @todo get class types with reflection: php_version >= 5.3
         *  $reflectedClass = new ReflectionClass($layerBlock);
         *  $property = $reflectedClass->getProperty('_attributeFilterBlockName');
         *  $property->setAccessible(true);
         *  $property->getValue($class);
         */
        $filterType = 'catalog/layer_filter_attribute';
        $filter = Mage::getModel($filterType)
            ->setAttributeModel($page->getAttribute())
            ->setLayer($layer);
        Mage::getResourceModel($filterType)
            ->applyFilterToCollection($filter, $page->getOption()->getOptionId());
    }
}
