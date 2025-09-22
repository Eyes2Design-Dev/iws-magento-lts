<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabel_Block_Sales_Order_Grid
{
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('upslabel_pdflabels', array(
            'label' => Mage::helper('sales')->__('Print UPS Shipping Labels'),
            'url' => $this->getUrl('upslabel/adminhtml_pdflabels'),
        ));

        return $this;
    }
}
