<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'upslabel';
        $this->_controller = 'adminhtml_upslabel';
        
        $this->_updateButton('save', 'label', Mage::helper('upslabel')->__('Save Item'));
        //$this->_updateButton('delete', 'label', Mage::helper('upslabel')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('upslabel_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'upslabel_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'upslabel_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('upslabel_data') && Mage::registry('upslabel_data')->getId() ) {
            return Mage::helper('upslabel')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('upslabel_data')->getTitle()));
        } else {
            //return Mage::helper('upslabel')->__('Add Item');
        }
    }
}