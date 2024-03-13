<?php
/*********************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Webforms/model/WebformsModel.php';
require_once 'include/Webservices/DescribeObject.php';

class ITS4YouCreator
{
    protected static $moduleDescribeCache = array();
    public $moduleName = 'ITS4YouCreator';
    public $LBL_MODULE_NAME = 'ITS4YouCreator';
    public $LBL_MODULE_LABEL = 'Creator 4 You';
    public $db;
    public $log;
    /**
     * [module, type, label, url, icon, sequence, handlerInfo]
     * @return array
     */
    public $registerCustomLinks = array(
        ['ITS4YouCreator', 'HEADERSCRIPT', 'ITS4YouCreator_HS_Js', 'layouts/$LAYOUT$/modules/Settings/ITS4YouCreator/resources/ITS4YouCreator_HS.js']
    );

    public function __construct()
    {
        global $log, $currentModule;
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    public function vtlib_handler($moduleName, $eventType)
    {
        switch ($eventType) {
            case 'module.postinstall':
            case 'module.enabled':
            case 'module.postupdate':
                $this->addCustomLinks();
                break;
            case 'module.disabled':
            case 'module.preupdate':
                $this->deleteCustomLinks();
                break;
            case 'module.preuninstall':
                $this->deleteCustomLinks();
                $this->disableCreatorFields();
                break;
        }
    }

    public function disableCreatorFields()
    {
        $this->db->pquery(
            'UPDATE vtiger_field SET presence=? WHERE columnname=? AND tablename=? AND fieldname=?',
            array(1, 'smcreatorid', 'vtiger_crmentity', 'creator')
        );
    }

    public function addCustomLinks()
    {
        $this->updateSettingsLink();
        $this->updateCustomLinks();

        ITS4YouCreator_Module_Model::showCreators();
    }

    public function updateSettingsLink($register = true)
    {
        $image = '';
        $description = 'Creator field for modules.';
        $linkTo = 'index.php?module=ITS4YouCreator&parent=Settings&view=Index';

        $this->db->pquery('DELETE FROM vtiger_settings_field  WHERE name=?', array($this->LBL_MODULE_LABEL));

        if ($register) {
            $fieldId = $this->db->getUniqueID('vtiger_settings_field');
            $blockId = getSettingsBlockId('LBL_OTHER_SETTINGS');
            $sequence = intval($this->db->pquery('SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid=?', array($blockId)));
            $sequence++;

            $this->db->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)', [$fieldId, $blockId, $this->LBL_MODULE_LABEL, $image, $description, $linkTo, $sequence]);
        }

    }

    /**
     * @param bool $register
     */
    public function updateCustomLinks($register = true)
    {
        foreach ($this->registerCustomLinks as $customLink) {
            $module = Vtiger_Module::getInstance($customLink[0]);
            $type = $customLink[1];
            $label = $customLink[2];
            $url = str_replace('$LAYOUT$', Vtiger_Viewer::getDefaultLayoutName(), $customLink[3]);

            if ($module) {
                $module->deleteLink($type, $label);

                if ($register) {
                    $module->addLink($type, $label, $url, $customLink[4], $customLink[5], $customLink[6]);
                }
            }
        }
    }

    public function deleteCustomLinks()
    {
        $this->updateSettingsLink(false);
        $this->updateCustomLinks(false);
    }
}
