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
class PH2M_PhEmailTester_Block_Adminhtml_System_Email_Tester_From extends Mage_Core_Block_Template
{
    public function getTemplates()
    {
        $templates              = $this->getMagentoDefaultEmailTemplates();
        $customTemplatesData    = $this->getCustomEmailTemplatesAndData();

        if(!$customTemplatesData) { return $templates; }
        foreach ($templates as $i => $template) {
            if (array_key_exists($template['value'], $customTemplatesData)) {
                $templates[$i]['value'] = ['config' => $templates[$i]['value']];
                foreach ($customTemplatesData[$template['value']] as  $columnName => $columnValue) {
                    $templates[$i]['value'][$columnName] = $columnValue;
                }
            } else {
                $templates[$i]['value'] = ['code' => $template['value']];
            }
        }

        return $templates;
    }

    protected function getMagentoDefaultEmailTemplates()
    {
        $templates = Mage::getModel('core/email_template')->getDefaultTemplatesAsOptionsArray();
        return $templates;
    }

    protected function getCustomEmailTemplatesAndData()
    {
        $xmlTemplateData    = [];
        $file               = Mage::getConfig()->getModuleDir('etc', 'PH2M_PhEmailTester') . DS . 'templates.xml';
        if (!file_exists($file)) { return false; }
        $xml                = simplexml_load_file($file);
        if (!isset($xml->templates)) { return false; }
        $nodeTemplate = (array) $xml->templates;

        foreach ($nodeTemplate as $id => $template) {
            $xmlTemplateData[$id] = (array) $template;
        }

        return $xmlTemplateData;

    }

    public function getStores()
    {
        return Mage::app()->getStores();
    }
}