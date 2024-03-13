<?php
/*********************************************************************************
 * The content of this file is subject to the Reset CP Password 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Webforms/model/WebformsModel.php';
require_once 'include/Webservices/DescribeObject.php';

class ITS4YouResetCPPassword
{
    protected static $moduleDescribeCache = array();
    public $moduleName = 'ITS4YouResetCPPassword';
    public $linkDetail;
    // Cache to speed up describe information store
    public $linkHeader;

    /**
     * [module, type, label, url, icon, sequence, handlerInfo]
     * @return array
     */
    public $registerCustomLinks = array();

    /**
     * @var PearDatabase
     */
    public $db;

    /**
     * @var Logger
     */
    public $log;

    function __construct()
    {
        global $log, $currentModule;

        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function vtlib_handler($moduleName, $eventType)
    {
        $this->retrieveCustomLinks();

        switch ($eventType) {
            case 'module.postinstall':
            case 'module.enabled':
            case 'module.postupdate':
                $this->addCustomLinks();
                break;
            case 'module.disabled':
            case 'module.preuninstall':
            case 'module.preupdate':
                $this->deleteCustomLinks();
                break;
        }
    }

    public function addCustomLinks()
    {
        $this->updateCustomLinks();
    }

    /**
     * @param bool $register
     */
    public function updateCustomLinks($register = true)
    {
        foreach ($this->registerCustomLinks as $customLink) {
            list($moduleName, $type, $label, $url, $icon, $sequence, $handler) = array_pad($customLink, 7, null);
            $module = Vtiger_Module::getInstance($moduleName);
            $url = str_replace('$LAYOUT$', Vtiger_Viewer::getDefaultLayoutName(), $url);

            if ($module) {
                $module->deleteLink($type, $label);

                if ($register) {
                    $module->addLink($type, $label, $url, $icon, $sequence, $handler);
                }
            }
        }
    }

    public function deleteCustomLinks()
    {
        $this->updateCustomLinks(false);
    }

    public function retrieveCustomLinks()
    {
        require_once('include/utils/utils.php');
        include_once('vtlib/Vtiger/Module.php');

        $moduleName = $this->moduleName;
        $moduleInstance = Vtiger_Module::getInstance('Contacts');
        $this->linkDetail = 'DETAILVIEWBASIC';
        $this->linkHeader = 'modules/' . $moduleName . '/' . $moduleName . '.js';

        $layout = Vtiger_Viewer::getDefaultLayoutName();

        if ($layout == 'v7') {
            $this->linkDetail = 'DETAILVIEW';
            $this->linkHeader = 'layouts/v7/modules/' . $moduleName . '/resources/Detail.js';
        }

        $this->registerCustomLinks[] = ['Contacts', 'HEADERSCRIPT', 'ITS4YouResetCPPassword_Detail_Js', $this->linkHeader];
        $this->registerCustomLinks[] = ['Contacts', $this->linkDetail, 'Reset CP Password', 'javascript:ITS4YouResetCPPassword_Detail_Js.ResetPassword($RECORD$)'];
    }
}