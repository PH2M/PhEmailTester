<?php
/**
 * PH2M_PhEmailTester
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   PhEmailTester
 * @copyright  Copyright (c) 2018 PH2M SARL
 * @author     PH2M SARL <contact@ph2m.com> : http://www.ph2m.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PH2M_PhEmailTester_Adminhtml_System_Email_TesterController extends Mage_Adminhtml_Controller_Action
{


    protected $_modelInstance = [];
    protected $_modelLoaded   = [];

    /**
     * Display cache management grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Email tester'));

        $this->loadLayout()
            ->_setActiveMenu('system/email_template/tester')
            ->renderLayout();
    }
    
    public function renderTemplateAction()
    {
        $this->_getMagentoModelInstance();
        $emailCode      = $this->getRequest()->getParam('code');
        $store          = $this->getRequest()->getParam('store');
        Mage::app()->setCurrentStore($store);
        if(!$emailCode) {
            $emailCode = Mage::getStoreConfig($this->getRequest()->getParam('config'), $store);
        }
        $email          = Mage::getModel('core/email_template')->loadDefault($emailCode);
        $params         = $this->getRequest()->getParams();
        $params['store']  = Mage::app()->getStore();

        foreach ($params as $key => $value) {
            if(array_key_exists($key, $this->_modelInstance)) {
                $params[$key] = $this->_modelInstance[$key]->load($value);
            } elseif(array_key_exists($key, $this->_modelLoaded)) {
                $params[$key] = $this->_modelLoaded[$key];

            }
        }
        $html           = $email->getProcessedTemplate($params);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('result' => 1, 'html' => $html)));
    }

    /**
     * Check if cache management is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/email_template/tester');
    }

    protected function _getMagentoModelInstance()
    {
        $file               = Mage::getConfig()->getModuleDir('etc', 'PH2M_PhEmailTester') . DS . 'templates.xml';
        if (!file_exists($file)) { return false; }
        $xml                = simplexml_load_file($file);
        if (!isset($xml->models)) { return false; }
        $nodeModels = (array) $xml->models;

        foreach ($nodeModels as $id => $model) {
            $this->_modelInstance[$id] = Mage::getModel($model);
        }
        if (!isset($xml->default_id)) { return false; }
        $nodeIds = (array) $xml->default_id;
        foreach ($nodeIds as $model => $id) {
            $this->_modelLoaded[$model] = $this->_modelInstance[$model]->load($id);
        }
    }

}