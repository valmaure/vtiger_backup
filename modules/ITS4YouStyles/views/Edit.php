<?php
/*********************************************************************************
 * The content of this file is subject to the EMAIL Maker license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ******************************************************************************* */

class ITS4YouStyles_Edit_View extends Vtiger_Edit_View
{

    function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.ITS4YouStyles.resources.CodeMirror.lib.codemirror",
            "modules.ITS4YouStyles.resources.CodeMirror.mode.javascript.javascript",
            "modules.ITS4YouStyles.resources.CodeMirror.addon.selection.active-line",
            "modules.ITS4YouStyles.resources.CodeMirror.addon.edit.matchbrackets"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);

        $cssFileNames = array(
            '~/modules/ITS4YouStyles/resources/CodeMirror/lib/codemirror.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}