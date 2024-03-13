<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ITS4YouStyles_Module_Model extends Vtiger_Module_Model
{
    static $mobileIcon = 'palette';
    var $defid = false;
    var $All_Related_Records = false;

    public function getSettingLinks()
    {
        $settingsLinks = parent::getSettingLinks();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleName = $this->getName();

        if ($currentUserModel->isAdminUser()) {

            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_MODULE_REQUIREMENTS',
                'linkurl' => 'index.php?module=ITS4YouInstaller&parent=Settings&view=Requirements&mode=Module&sourceModule=ITS4YouStyles',
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_LICENSE',
                'linkurl' => 'index.php?module=ITS4YouInstaller&view=License&parent=Settings&sourceModule=ITS4YouStyles',
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_UPGRADE',
                'linkurl' => 'index.php?module=ModuleManager&parent=Settings&view=ModuleImport&mode=importUserModuleStep1',
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'LBL_UNINSTALL',
                'linkurl' => 'index.php?module=ITS4YouInstaller&view=Uninstall&parent=Settings&sourceModule=ITS4YouStyles',
            );
        }

        return $settingsLinks;
    }

    public function isSummaryViewSupported()
    {
        return true;
    }

    function showITS4YouStyles(Vtiger_Request $request, $viewer)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if (substr($record, 0, 1) == "t") {
            $record = substr($record, 1);
        }

        $label = $request->get('tab_label');
        $viewer->assign('VIEW', $request->get('view'));

        $ITS4YouStyles_Header = array("name" => 'Name', "priority" => "Priority", "assigned_to" => 'Assigned To');
        $viewer->assign('RELATED_HEADERS', $ITS4YouStyles_Header);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign("TEMPLATEID", $record);

        $Related_Records = $this->getRelatedRecords($record, $moduleName, 'asc');

        $Template_Permissions_Data = array();
        $ModuleInstance = $moduleName . "_" . $moduleName . "_Model";

        if (class_exists($ModuleInstance)) {
            $ModuleInstanceModel = new $ModuleInstance();
            if (method_exists($ModuleInstanceModel, "returnTemplatePermissionsData")) {
                $Template_Permissions_Data = $ModuleInstanceModel->returnTemplatePermissionsData("", $record);
            }
        }

        if ($Template_Permissions_Data["edit"]) {
            $viewer->assign("IS_DELETABLE", "yes");
            $viewer->assign("EDIT", "permitted");
        }

        if (Users_Privileges_Model::isPermitted("ITS4YouStyles", 'EditView')) {
            $viewer->assign("IS_EDITABLE", "yes");
        }

        $viewer->assign('RELATED_RECORDS', $Related_Records);
        echo $viewer->view('DetailViewRelatedList.tpl', 'ITS4YouStyles', 'true');
    }

    public function getRelatedRecords($parentid, $parentmodule, $ordering = "desc", $control_editable = false)
    {

        $ordering = strtolower($ordering);

        if ($ordering == 'asc') {
            $other_order = 'desc';
        } else {
            $other_order = 'asc';
        }

        if (!isset($this->All_Related_Records[$parentid][$ordering]) && (isset($this->All_Related_Records[$parentid][$other_order]) && count($this->All_Related_Records[$parentid][$other_order]) == 1)) {
            $setid = $parentid;
            $this->All_Related_Records[$setid][$ordering] = $this->All_Related_Records[$setid][$other_order];
        } else {
            if ($parentid != "" && $parentmodule != "" && !isset($this->All_Related_Records[$parentid][$ordering])) {
                $this->getRelatedList($parentid, $parentmodule, $ordering);
                $setid = $parentid;
            } else {
                $setid = $this->defid;
            }
        }
        if (isset($this->All_Related_Records[$setid][$ordering])) {
            $Related_Records = $this->All_Related_Records[$setid][$ordering];

            if ($control_editable) {
                foreach ($Related_Records as &$Related_Record_Data) {
                    $recordPermission = Users_Privileges_Model::isPermitted('ITS4YouStyles', 'EditView', $Related_Record_Data["id"]);
                    if ($recordPermission) {
                        $Related_Record_Data["iseditable"] = 'yes';
                    }
                }
            }

        } else {
            $Related_Records = array();
        }

        return $Related_Records;
    }

    public function getRelatedList($parentid, $module, $ordering = 'asc', $control_editable = false)
    {

        $adb = PearDatabase::getInstance();

        $ordering = strtolower($ordering);

        if (!isset($this->All_Related_Records[$parentid][$ordering])) {
            $this->All_Related_Records[$parentid][$ordering] = array();
        }

        $query = "SELECT its4you_styles.*, vtiger_crmentity.* FROM its4you_styles 
                INNER JOIN vtiger_crmentity 
                 ON vtiger_crmentity.crmid = its4you_styles.styleid
                INNER JOIN its4you_stylescf 
                 ON its4you_stylescf.styleid = its4you_styles.styleid
                INNER JOIN its4you_stylesrel 
                 ON its4you_stylesrel.styleid = its4you_styles.styleid  
                WHERE vtiger_crmentity.deleted = '0' AND its4you_stylesrel.parentid = ? AND its4you_stylesrel.module = ?";
        $query .= " ORDER BY its4you_styles.priority " . $ordering . ", its4you_styles.styleid " . $ordering;
        $list_result = $adb->pquery($query, array($parentid, $module));
        $num_rows = $adb->num_rows($list_result);

        if ($num_rows > 0) {
            while ($row = $adb->fetchByAssoc($list_result)) {
                $assigned_to_name = getUserFullName($row["smownerid"]);
                $this->All_Related_Records[$parentid][$ordering][] = array("id" => $row["styleid"], "name" => $row["stylename"], "priority" => $row["priority"], "stylecontent" => $row["stylecontent"], "assigned_to" => $assigned_to_name, "iseditable" => "");
            }
        }
    }

    public function loadStyles($parentid, $parentmodule, $ordering = "desc")
    {
        $this->defid = $parentid;
        $this->getRelatedList($parentid, $parentmodule, $ordering);
    }

    public function addStyles($content, $parentid = "", $parentmodule = "", $ordering = "desc")
    {

        $Related_Records = $this->getRelatedRecords($parentid, $parentmodule, $ordering);

        if (count($Related_Records) > 0) {

            $styles_content = '';
            foreach ($Related_Records as $RData) {
                $styles_content .= '<style>' . decode_html($RData['stylecontent']) . '</style>';
            }

            if (empty($content)) {
                $content = '<!DOCTYPE html>
                                <html>
                                <head>' . $styles_content . '</head>
                                <body></body>
                                </html>';
            } else {

                ITS4YouStyles_Module_Model::getSimpleHtmlDomFile();

                if (function_exists('str_get_html')) {
                    $html = str_get_html($content);

                    if (is_array($html->find("head")) && count($html->find("head")) > 0) {

                        foreach ($html->find("head") as $head) {
                            $head_content = $head->innertext;
                            $head->innertext = $styles_content . $head_content;
                        }
                        $content = $html->save();

                    } else {
                        $content = '<!DOCTYPE html>
                            <html>
                            <head>' . $styles_content . '</head>
                            <body>
                            ' . $content . '
                            </body>
                            </html>';
                    }
                } else {
                    $content = "<!-- no simple html dom file exists -->" . $content;
                }
            }
        }
        return $content;
    }

    public static function getSimpleHtmlDomFile()
    {

        if (!class_exists('simple_html_dom_node')) {
            $pdfmaker_simple_html_dom = "modules/PDFMaker/resources/simple_html_dom/simple_html_dom.php";
            $emailmaker_simple_html_dom = "modules/EMAILMaker/resources/simple_html_dom/simple_html_dom.php";

            if (file_exists($pdfmaker_simple_html_dom)) {
                $file = $pdfmaker_simple_html_dom;
            } elseif (file_exists($emailmaker_simple_html_dom)) {
                $file = $emailmaker_simple_html_dom;
            } else {
                $file = "include/simplehtmldom/simple_html_dom.php";
            }
        }

        if (!empty($file)) {
            require_once $file;
        }
    }

    public function getStyleFiles($parentid, $parentmodule, $ordering = "desc")
    {

        $files_content = "";
        $Files = array();
        $site_URL = vglobal('site_URL');
        $Related_Records = $this->getRelatedRecords($parentid, $parentmodule, $ordering);

        if (count($Related_Records) > 0) {

            $styles_content = '';
            foreach ($Related_Records as $RData) {
                $style_file = "modules/ITS4YouStyles/resources/files/style_" . $RData['id'] . ".css";
                if (!file_exists($style_file)) {
                    $fh = fopen($style_file, 'w');
                    fwrite($fh, $RData["stylecontent"]);
                    fclose($fh);
                }

                $Files[] = "'" . $style_file . "'";
            }

            $files_content = implode(",", $Files);
        }
        return $files_content;
    }

    public function getStyleContent($parentid, $parentmodule, $ordering = 'desc')
    {

        return $this->getRelatedRecords($parentid, $parentmodule, $ordering, true);
        // $this->getRelatedList($parentid,$parentmodule,$ordering,true);
    }

    public function duplicateRelatedRecords($sourceid, $newid, $parentmodule)
    {

        $Related_Records = $this->getRelatedRecords($sourceid, $parentmodule);
        $adb = PearDatabase::getInstance();

        foreach ($Related_Records as $RData) {
            $Atr = array($RData["id"], $newid, $parentmodule);
            $num_rows = $adb->num_rows($adb->pquery("SELECT styleid FROM its4you_stylesrel WHERE styleid = ? AND parentid = ? AND module = ?", $Atr));
            if (!$num_rows) {
                $adb->pquery("INSERT INTO its4you_stylesrel (styleid, parentid, module) VALUES (?,?,?)", $Atr);
            }
        }
    }

    /**
     * @return array
     */
    public function getDatabaseTables()
    {
        return [
            'its4you_styles',
            'its4you_stylescf',
            'its4you_stylesrel',
            'vtiger_its4youstyles_user_field',
        ];
    }

}