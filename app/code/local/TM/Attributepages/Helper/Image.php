<?php

class TM_Attributepages_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_entity;
    protected $_mode;

    public function init($entity, $mode = 'image')
    {
        $this->_entity = $entity;
        $this->_mode   = $mode;
        return $this;
    }


    public function resize($width, $height)
    {
        if (!$imageUrl = $this->getImageUrl()) {
            return '';
        }

        if (!$width || !is_numeric($width)) {
            $width = 200;
        }
        if (!$height || !is_numeric($height)) {
            $height = $width;
        }

        $dir = Mage::getBaseDir('media')
            . DS . "tm"
            . DS . "attributepages"
            . DS . "resized";

        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        };

        $imageName = substr(strrchr($imageUrl, "/"), 1);
        $imageName = $width . '_' . $height . '_' . $imageName;

        $imageResized = $dir . DS . $imageName;

        $imagePath = str_replace(Mage::getBaseUrl('media'), 'media/', $imageUrl);
        $imagePath = Mage::getBaseDir() . DS . str_replace("/", DS, $imagePath);

        if (!file_exists($imageResized) && file_exists($imagePath)) {
            $imageObj = new Varien_Image($imagePath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(true);
            // $imageObj->keepTransparency(true);
            $imageObj->backgroundColor($this->getBackgroundColor());
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }
        $imageUrl = Mage::getBaseUrl('media')
            . "tm/attributepages/resized/"
            . $imageName;

        return $imageUrl;
    }

    public function getBackgroundColor()
    {
        $rgb = Mage::getStoreConfig('attributepages/image/background');
        $rgb = explode(',', $rgb);
        foreach ($rgb as $i => $color) {
            $rgb[$i] = (int) $color;
        }
        return $rgb;
    }

    public function getImageUrl()
    {
        $image = $this->_entity->getData($this->_mode);
        if (empty($image)) {
            return false;
        }
        return Mage::getBaseUrl('media')
            . 'tm/attributepages'
            . $image;
    }
}
