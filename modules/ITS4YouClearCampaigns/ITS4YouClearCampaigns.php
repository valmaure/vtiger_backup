<?php
/*********************************************************************************
 * The content of this file is subject to the Clear Campaigns 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Webforms/model/WebformsModel.php';
require_once 'include/Webservices/DescribeObject.php';

class ITS4YouClearCampaigns {
    // Cache to speed up describe information store
    protected static $moduleDescribeCache = array();
    public $customscript;
    public $links;
    public $linkto;

    function __construct() {
        global $log, $currentModule;

        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function vtlib_handler($moduleName, $eventType) {

        require_once('include/utils/utils.php');
        include_once('vtlib/Vtiger/Module.php');

        $moduleInstance = Vtiger_Module::getInstance('Campaigns');
        $this->links = array("Contact"=> "Contacts","Lead"=> "Leads","Organization"=> "Accounts");

        
        $this->linkto = 'DETAILVIEWBASIC';
        $this->customscript = 'modules/'.$moduleName.'/'.$moduleName.'.js';
        
        $layout = Vtiger_Viewer::getDefaultLayoutName();
        if($layout == "v7") {
            $moduleInstance->deleteLink('HEADERSCRIPT', 'ITS4YouClearCampaignsJS',$this->customscript);
            $this->linkto = 'DETAILVIEW';
            $this->customscript = 'layouts/v7/modules/'.$moduleName.'/resources/Detail.js';
        }

        if($eventType == 'module.postinstall') {
            $this->addCustomLinks($moduleInstance);
        } else if($eventType == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
            $this->deleteCustomLinks($moduleInstance);
        } else if($eventType == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
            $this->addCustomLinks($moduleInstance);
        } else if($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
            $this->deleteCustomLinks($moduleInstance);
        } else if($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    public function addCustomLinks($moduleInstance) {
        $moduleInstance->addLink('HEADERSCRIPT', 'ITS4YouClearCampaignsJS',$this->customscript);

        foreach ($this->links as $label => $list){
            $moduleInstance->addLink($this->linkto, 'Clear '.$label.' List','javascript:ITS4YouClearCampaigns_Detail_Js.ClearCampaign($RECORD$,"'.$list.'")');
        }
    }

    public function deleteCustomLinks($moduleInstance) {
        $moduleInstance->deleteLink('HEADERSCRIPT', 'ITS4YouClearCampaignsJS',$this->customscript);

        foreach ($this->links as $label => $list){
            $moduleInstance->deleteLink($this->linkto, 'Clear '.$label.' List','javascript:ITS4YouClearCampaigns_Detail_Js.ClearCampaign($RECORD$,"'.$list.'")');
        }
    }

}