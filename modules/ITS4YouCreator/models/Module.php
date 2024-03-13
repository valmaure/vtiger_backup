<?php
/*********************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

class ITS4YouCreator_Module_Model extends Vtiger_Module_Model
{

    public static $mobileIcon = 'user-plus';

    public function getSettingLinks()
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();

        $settingsLinks = [];
        if ($currentUser->isAdminUser()) {

            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_INTEGRATION',
                'linkurl' => $this->getDefaultUrl(),
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_MODULE_REQUIREMENTS',
                'linkurl' => 'index.php?module=ITS4YouInstaller&parent=Settings&view=Requirements&mode=Module&sourceModule=ITS4YouCreator',
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_LICENSE',
                'linkurl' => 'index.php?module=ITS4YouInstaller&view=License&parent=Settings&sourceModule=ITS4YouCreator',
            );
            $settingsLinks[] = array(
                "linktype" => "LISTVIEWSETTING",
                "linklabel" => "LBL_UPGRADE",
                "linkurl" => "index.php?module=ModuleManager&parent=Settings&view=ModuleImport&mode=importUserModuleStep1",
            );
            $settingsLinks[] = array(
                "linktype" => "LISTVIEWSETTING",
                "linklabel" => "LBL_UNINSTALL",
                "linkurl" => "index.php?module=ITS4YouInstaller&view=Uninstall&parent=Settings&sourceModule=ITS4YouCreator",
            );
        }

        return $settingsLinks;
    }

    public function getDefaultUrl()
    {
        return 'index.php?parent=Settings&module=ITS4YouCreator&view=Index';
    }

    public static function getBlockIdFromFields($fields)
    {
        $fieldNames = array('createdtime', 'smownerid', 'smcreatorid', 'modifiedby');

        foreach ($fieldNames as $fieldName) {
            if (!empty($fields[$fieldName]['block'])) {
                return intval($fields[$fieldName]['block']);
            }
        }

        return 0;
    }

    public static function updateModuleField($tabId, $mode, $fieldName = 'creator')
    {
        $adb = PearDatabase::getInstance();
        $fields = self::getActiveFields()[$tabId];
        $blockId = self::getBlockIdFromFields($fields);
        $moduleName = getTabModuleName($tabId);
        $return = false;

        if (!empty($moduleName)) {
            $presence = 1;
            $blockName = getBlockName($blockId);
            $fieldLabel = 'Creator';
            $columnName = 'smcreatorid';

            if ('modifiedby' === $fieldName) {
                $fieldLabel = 'Last Modified By';
                $columnName = 'modifiedby';
            }

            if ('Show' === $mode) {
                $presence = 2;

                include_once 'vtlib/Vtiger/Module.php';
                include_once 'vtlib/Vtiger/Field.php';
                include_once 'vtlib/Vtiger/Block.php';

                $module = Vtiger_Module::getInstance($moduleName);
                $block = Vtiger_Block::getInstance($blockName, $module);
                $field = Vtiger_Field_Model::getInstance($fieldName, $module);

                if (!$field) {
                    $field = new Vtiger_Field();
                }

                $field->name = $fieldName;
                $field->label = $fieldLabel;
                $field->table = 'vtiger_crmentity';
                $field->column = $columnName;
                $field->uitype = 52;
                $field->typeofdata = 'V~O';
                $field->displaytype = 2;
                $field->headerfield = 0;
                $field->presence = $presence;

                if ($block && is_object($block)) {
                    $field->save($block);
                }
            }

            $adb->pquery('UPDATE vtiger_field SET presence=?, displaytype=? WHERE tabid=? AND columnname=?', array($presence, 2, $tabId, $columnName));

            $return = true;
        }


        return $return;
    }

    public static function getActiveFields()
    {
        $adb = PearDatabase::getInstance();
        $query = 'SELECT tabid, columnname, block, fieldid, presence FROM vtiger_field WHERE columnname = ? OR columnname = ? OR columnname = ? OR columnname = ? ORDER BY tabid';
        $params = array('smcreatorid', 'modifiedby', 'createdtime', 'smownerid');
        $result = $adb->pquery($query, $params);
        $num_rows = $adb->num_rows($result);
        $fields = array();

        while($field = $adb->fetchByAssoc($result)) {
            $tabId = $field['tabid'];

            if (2 === intval($field['presence'])) {
                if ('smcreatorid' === $field['columnname']) {
                    $fields[$tabId]['creator_active'] = true;
                }

                if ('modifiedby' === $field['columnname']) {
                    $fields[$tabId]['modified_by_active'] = true;
                }
            }

            $fields[$tabId][$field['columnname']] = $field;
        }

        return $fields;
    }

    public static function showCreators()
    {
        $modules = self::getEntityModules();

        foreach ($modules as $module) {
            self::updateModuleField($module->id, 'Show');
        }
    }

    public function disableCreatorFields()
    {
        $adb = PearDatabase::getInstance();
        $adb->pquery('UPDATE vtiger_field SET presence=? WHERE columnname=? AND tablename=? AND fieldname=?', array(1, 'smcreatorid', 'vtiger_crmentity', 'creator'));
    }
}