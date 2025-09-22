<?php
class ModernizedMedia_PurchaseWizard_Helper_Wizard extends Mage_Core_Helper_Abstract{
    //where the categories that are to be displayed in wizard live
    const PARENT_CATEGORY_ID = 128;

    const KIT = 1;
    const PACK = 2;

    public function getProducts($params){
        $typeID = (int) $params['typeID'];

        //Don't wanna expose everything
        $sanatizedProducts = array();
        if ($typeID == self::KIT){
            $sanatizedProducts['kit']=array();
            $products = $this->getKits($params);

            //Format the data
            foreach($products['kit'] as $p){
                if($this->doShow($p)){
                    $tierPrice = $p->getTierPrice();

                    $sanatizedProducts['kit'][] = array(
                        'entity_id'=> $p->getData('entity_id'),
                        'name'=> $p->getData('name'),
                        'price'=> $this->getActualPrice($p),
                        'tier_price'=> $tierPrice
                    );
                }
            }
        } else if ($typeID == self::PACK){
            $products = $this->getPacks($params);

            //Format the data
            foreach($products as $packType=>$c){
                foreach($c as $p){
                    if($this->doShow($p)){
                        $tierPrice = $p->getTierPrice();
                        $sanatizedProducts[$packType][] = array(
                            'entity_id'=> $p->getData('entity_id'),
                            'name'=> $p->getData('name'),
                            'price'=> $this->getActualPrice($p),
                            'tier_price'=> $tierPrice
                        );
                    }
                }
            }
        } else {
            //log error
            Mage::log("Purchase Wizard ERROR: Product type id: [" . $typeID . "] is unknown, unable to retrieve products.");
        }

        return $sanatizedProducts;
    }

    /**
     * @param $product Mage_Catalog_Model_Product
     * @return mixed
     */
    private function getActualPrice($product){
        $price = $product->getPrice();
        $specialPrice = $product->getSpecialPrice();

        $MageDate = Mage::getModel('core/date');
        $today = $MageDate->date();
        $isSpecial = ($today >= $MageDate->date($product->getSpecialFromDate())) && ($today <= $MageDate->date(null, $product->getSpecialToDate()));

        return ($isSpecial && !empty($specialPrice)) ? $specialPrice : $price;
    }

    private function doShow($product){
        return ($product->getData('status') == 1 && $product->getData('visibility') == 1);
    }

    //Categories have a name and Depth (Museum 1.25", Medium 1.5", etc)
    public function getStretchBarCategories(){
        $_category = Mage::getModel('catalog/category')->load(self::PARENT_CATEGORY_ID);

        //This only get active categories
        return $_category->getChildrenCategories();

        //this gets active and inactive
        // return Mage::getResourceModel('catalog/category_collection')
        //         ->addAttributeToSelect('*')
        //         ->addAttributeToFilter('is_active', array('in' => array(0,1)))
        //         ->addAttributeToFilter('parent_id', $_category->getId());
    }

    private function getPacks($params){
        $categoryID = (int) $params['categoryID'];

        //Get Sub Categories
        $subCategories = $this->getApplicableSubCategories($categoryID,self::PACK);

        //Get Products of Category. There are different categories here
        $products = array();
        foreach($subCategories as $c){
            $products[$this->getPackType($c)] = $this->getCategoryProducts($c->getId());
        }

        return $products;
    }

    private function getPackType($category){
        $categoryName = $category->getName();
        $packType = '';
        if (strstr($categoryName, '2 Bar Paks' )){
            $packType = "singlePack";
        } else if (strstr($categoryName, '8 Bar Paks' )){
            $packType = "fourPack";
        } else if (strstr($categoryName, '24 Bar Paks' )){
            $packType = "twelvePack";
        } else {
            //log error
            Mage::log("Purchase Wizard ERROR: Unable to determine packType for category [" . $categoryName . "]. Really only 2 Bar Paks is configured to work.");
        }

        return $packType;
    }

    private function getApplicableSubCategories($categoryID, $applicableType){
        $category = Mage::getModel('catalog/category')->load($categoryID);

        $applicableCategories = array();

        foreach($category->getChildrenCategories() as $c){
            $isKit = $this->isKit($c);
            $isPak = $this->isPak($c);

            if ($applicableType == self::KIT && $isKit){
                $applicableCategories[]=$c;
            } else if ($applicableType == self::PACK && $isPak){
                $applicableCategories[]=$c;
            }
        }

        return $applicableCategories;
    }

    //TODO create attribute to determine this
    private function isKit($category){
        return (boolean) strstr($category->getName(), "Kits");
    }

    //TODO create attribute to determine this
    private function isPak($category){
        return (boolean) strstr($category->getName(), "Paks");
    }

    private function getKits($params){
        $categoryID = (int) $params['categoryID'];

        //Get Sub Categories
        $subCategories = $this->getApplicableSubCategories($categoryID,self::KIT);

        //Get Products of Category
        $products = array();
        $products['kit'] = array();
        foreach($subCategories as $c){
            $products['kit'] = array_merge($products['kit'],$this->getCategoryProducts($c->getId()));
        }

        return $products;
    }

    //This a bit weird because products aren't in a category. They are saved as associated products
    private function getCategoryProducts($categoryID){
        $category = Mage::getModel('catalog/category')->load($categoryID);

        $prodCollection = Mage::getModel('catalog/category')->load($category->getId())
                                ->getProductCollection()
                                ->addAttributeToSelect('*') // add all attributes - optional
                                ->addAttributeToFilter('status', 1); // enabled

        //TODO 
        $categoryProduct = array();
        foreach($prodCollection as $p){
            $categoryProduct=$p;
            break;
        }

        $associatedProducts = $categoryProduct->getTypeInstance(true)->getAssociatedProducts($categoryProduct);

        return $associatedProducts;
    }
}
