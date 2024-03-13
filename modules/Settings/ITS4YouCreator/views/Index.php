<?php
/*******************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ***************************************************************************** */

class Settings_ITS4YouCreator_Index_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {

        $module = $request->getModule();
        $qualifiedmodule = $request->getModule(false);

        $viewer = $this->getViewer($request);

        $viewer->assign('MODULE_NAME', $module);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedmodule);
        $viewer->assign('ALL_MODULES', ITS4YouCreator_Module_Model::getEntityModules());
        $viewer->assign('ALL_FIELDS', ITS4YouCreator_Module_Model::getActiveFields());

        $viewer->view('Index.tpl', $qualifiedmodule);
    }
}