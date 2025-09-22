<?php

class TM_Easyslide_Adminhtml_Easyslide_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('templates_master/easyslide')
            ->_addBreadcrumb(Mage::helper('easyslide')->__('Manage Sliders'), Mage::helper('easyslide')->__('Manage Sliders'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('easyslide/adminhtml_easyslide'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $easyslide_id = $this->getRequest()->getParam('id');
        $easyslide_model = Mage::getModel('easyslide/easyslide')->load($easyslide_id);
        $easyslide_model->loadSlides();

        if ($easyslide_model->getId() || $easyslide_id == 0) {
            Mage::register('slider', $easyslide_model);

            $this->_initAction()
                ->_addBreadcrumb(Mage::helper('easyslide')->__('Edit Slider'), Mage::helper('easyslide')->__('Edit Slider'))
                ->_addContent($this->getLayout()->createBlock('easyslide/adminhtml_easyslide_edit')->setData('action', $this->getUrl('*/*/save')))
                ->_addLeft($this->getLayout()->createBlock('easyslide/adminhtml_easyslide_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('easyslide')->__('Slide does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost())  {
            try {
                $post_data = $this->getRequest()->getPost();
                $easyslide_id = $this->getRequest()->getParam('easyslide_id');
                $easyslide_model = Mage::getModel('easyslide/easyslide');
                $mediaPath = Mage::getBaseDir('media') . '/easyslide/';

                if (!empty($post_data['slides'])) {
                    foreach ($post_data['slides'] as $slideId => $values) {
                        if (!empty($values['deleteimage'])) {
                            @unlink($mediaPath . $values['deleteimage']);
                            $post_data['slides'][$slideId]['url'] = '';
                        }
                    }
                    foreach ($_FILES['slides']['name'] as $slideId => $values) {
                        if (empty($values['url'])) {
                            continue;
                        }

                        $destFolder = rtrim(BP, '/\\') . '/media/easyslide/';
                        if( !is_writable($destFolder) ) {
                            Mage::getSingleton('adminhtml/session')->addError('Destination folder is not writable or does not exists.');
                            continue;
                        }
                        $fileName = Varien_File_Uploader::getCorrectFileName($values['url']);
                        $destFile = $destFolder . $fileName;
                        $result = move_uploaded_file(
                            $_FILES['slides']['tmp_name'][$slideId]['url'],
                            $destFile
                        );
                        if ($result) {
                            chmod($destFile, 0777);
                            $post_data['slides'][$slideId]['url'] = $fileName;
                        } else {
                            $post_data['slides'][$slideId]['url'] = '';
                            Mage::getSingleton('adminhtml/session')->addError('File ' . $fileName . ' was not uploaded.');
                        }
                    }
                }
                $nivoData = array();
                $nivoData['effect'] = implode(',', $post_data['nivoeffect']);
                $nivoData['theme'] = $post_data['theme'];
                $nivoData['slices'] = $post_data['slices'];
                $nivoData['boxCols'] = $post_data['boxCols'];
                $nivoData['boxRows'] = $post_data['boxRows'];
                $nivoData['animSpeed'] = $post_data['animSpeed'];
                $nivoData['pauseTime'] = $post_data['pauseTime'];
                $nivoData['directionNav'] = $post_data['directionNav'];
                $nivoData['controlNav'] = $post_data['controlNav'];
                $nivoData['pauseOnHover'] = $post_data['pauseOnHover'];
                $nivoData['manualAdvance'] = $post_data['manualAdvance'];
                $serializeNivo = serialize($nivoData);
                $post_data['nivo_options'] = $serializeNivo;
                /*
                 * Add Nivo Slider Data
                */
                $easyslide_model->setId($easyslide_id)
                    ->setData($post_data);

                if ($easyslide_id !== null) {
                    $easyslide_model->setModifiedTime(new Zend_Db_Expr('NOW()'));
                } else {
                    $easyslide_model->setCreatedTime(new Zend_Db_Expr('NOW()'));
                }

                $easyslide_model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Slider was successfully saved')
                );

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $easyslide_model->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0 ) {
            try {
                $easyslide_model = Mage::getModel('easyslide/easyslide');

                $easyslide_model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Slide has been successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
}
