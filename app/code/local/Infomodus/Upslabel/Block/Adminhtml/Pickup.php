<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabel_Block_Adminhtml_Pickup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_pickup';
        $this->_blockGroup = 'upslabel';
        $this->_headerText = Mage::helper('upslabel')->__('Pickup Manager');
        $this->_addButtonLabel = Mage::helper('upslabel')->__('Add Pickup');
        parent::__construct();
    }
}