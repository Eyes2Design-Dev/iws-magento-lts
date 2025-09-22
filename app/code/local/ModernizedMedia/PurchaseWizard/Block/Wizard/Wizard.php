<?php

class ModernizedMedia_PurchaseWizard_Block_Wizard_Wizard extends Mage_Core_Block_Template {

    //Categories have a name and Depth (Museum 1.25", Medium 1.5", etc)
    public function getStretchBarCategories(){
        $helper = Mage::helper('purchasewizard/Wizard');
        
        return $helper->getStretchBarCategories();
    }

    public function getFormKey(){
        return Mage::getSingleton('core/session')->getFormKey();
    }

    //This a bit ugly. Multiple info is saved as title (a single string)
    public function extractPartsOfCategoryTitle($categoryName){
        $titleParts = array();

        $keyword = '';
        if(strstr($categoryName, 'Duty')){
            $keyword = "Duty";
        } else if (strstr($categoryName,'Depth')){
            $keyword = 'Depth';
        } else {
            //log error
            Mage::log("Purchase Wizard ERROR: Product/Category title not formatted correctly, can't determine the Duty or Depth. Category Name: [" . $categoryName . "]");
        }

        $titleParts['duty'] = substr($categoryName, 0, strpos($categoryName, $keyword)+strlen($keyword));
        $titleParts['dutyParts']['type'] = trim(substr($titleParts['duty'], 0, strpos($titleParts['duty'], ' ')));
        $titleParts['dutyParts']['duty'] = trim(substr($titleParts['duty'], strpos($titleParts['duty'], ' ')));

        $size = explode(' ',trim(trim(substr($categoryName, strlen($titleParts['duty'])),"-")));

        $titleParts['size'] = htmlspecialchars(trim($size[0],"-"));

        $titleParts['tagline']=$this->getTagline($titleParts['duty']);
        
        return $titleParts;
    }

    //ugly
    private function getTagline($duty){
        $tagline = '';

        if (stristr($duty,'museum')){
            $tagline .= 'low profile';
        } else if (stristr($duty,'medium')){
            $tagline .= 'most economical';
        } else if (stristr($duty,'standard')){
            $tagline .= 'most popular';
        } else if (stristr($duty,'xtra')){
            $tagline .= 'elite series';
        } else if (stristr($duty,'heavy')){
            $tagline .= 'professional series';
        }

        return $tagline;
    }

    //Ugly, this data not stored in db. Kinda stored as the single product in the category
    public function getShortDescription($categoryID){
        $desc = '';

        // &#8209; = html entity for non breaking -
        if ($categoryID == 134){
            $desc .= 'bars are 1&#8209;1/4" deep and 1&#8209;3/8" thick';
        } else if ($categoryID == 129){
            $desc .= 'bars are 1&#8209;1/2" deep and 1" thick';
        } else if ($categoryID == 135){
            $desc .= 'bars are 1&#8209;1/2" deep and 1&#8209;3/8" thick';
        } else if ($categoryID == 136){
            $desc .= 'bars are 1&#8209;3/4" deep and 1&#8209;3/8" thick';
        } else if ($categoryID == 137){
            $desc .= 'bars are 2&#8209;1/4" deep and 1&#8209;3/8" thick';
        } else {
            //log
            Mage::log("Purchase Wizard ERROR: Category [$categoryID] doesnt have configuration for short description");
        }

        return $desc;
    }

    //Ugly, this data not stored in db
    public function getCategoryImage($categoryID){
        $img = array();

        if ($categoryID == 134){
            $img['pack'] = 'wraptek_canvas_museum.png';
            $img['kit'] = 'wraptek_canvas_stretcher_kit.png';
        } else if ($categoryID == 129){
            $img['pack'] = 'wraptek_canvas_medium_duty.png';
            $img['kit'] = 'wraptek_canvas_stretcher_kit.png';
        } else if ($categoryID == 135){
            $img['pack'] = 'wraptek_canvas_standard_duty.png';
            $img['kit'] = 'wraptek_canvas_stretcher_kit.png';
        } else if ($categoryID == 136){
            $img['pack'] = 'wraptek_canvas_heavy_duty.png';
            $img['kit'] = 'wraptek_canvas_stretcher_kit.png';
        } else if ($categoryID == 137){
            $img['pack'] = 'wraptek_canvas_extra_heavy_duty.png';
            $img['kit'] = 'wraptek_canvas_kit_heavy_duty.png';
        } else {
            //log
            Mage::log("Purchase Wizard ERROR: Category [$categoryID] doesnt have configuration for img");
        }

        return $img;
    }

    //Ugly, this data not stored in db?
    public function getStretchLength($categoryID){
        $stretchLength = 0;

        if ($categoryID == 134){
            $stretchLength = 72;
        } else if ($categoryID == 129){
            $stretchLength = 60;
        } else if ($categoryID == 135){
            $stretchLength = 90;
        } else if ($categoryID == 136){
            $stretchLength = 100;
        } else if ($categoryID == 137){
            $stretchLength = 200;
        } else {
            //log
            Mage::log("Purchase Wizard ERROR: Category [$categoryID] doesnt have configuration for stretch length");
        }

        return $stretchLength;
    }

    private function isAllowedWizardDiscount($rule){
        $isAllowed = false;

        if ($rule['simple_action'] == 'by_percent' &&
            $rule['discount_amount'] > 0 ){
                $isAllowed = true;
        }

        //TODO check that customer group

        //TODO check date

        if (!$rule['is_active']){
            $isAllowed = false;
        }

        return $isAllowed;
    }

    public function getDiscountSteps(){
        $rules = Mage::getModel('salesrule/rule')->getCollection();
        $rules->getSelect()->where("name LIKE '%purchase_wizard%'");

        $sanatizedRules = array();

        foreach($rules->getData() as $rule){
            if (!$this->isAllowedWizardDiscount($rule))
                continue;

            $ruleActions = unserialize($rule['actions_serialized']);

            //discount_step is old news. If back to that replace calcStep
            $sanatizedRules[] = array(
                'discount_step' => $rule['discount_step'],
                'discount_amount' => (int)$rule['discount_amount'],
                'ruleActions' => $ruleActions,
                'calcStepLower' => $ruleActions['conditions'][0]['value'],
                'calcStepUpper' => (int) ($ruleActions['conditions'][1]['value'] - 1)
            );
        }

        usort($sanatizedRules, function($a,$b){
            return $a['calcStepLower'] > $b['calcStepLower'];
        });

        return $sanatizedRules;
    }

}
