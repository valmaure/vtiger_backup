<?php
/* * *******************************************************************************
 * The content of this file is subject to the PDF Maker license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class ITS4YouStyles_AddStyleAjax_View extends Vtiger_IndexAjax_View
{

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();

        if (!Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE_NAME', $moduleName);

        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        $moduleModel = $recordModel->getModule();

        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $requestFieldName => $requestFieldValue) {
            if (array_key_exists($requestFieldName, $fieldList)) {
                $moduleFieldModel = $fieldList[$requestFieldName];
                $recordModel->set($requestFieldName, $moduleFieldModel->getDBInsertValue($requestFieldValue));
            }
        }

        $fieldsInfo = array();
        foreach ($fieldList as $name => $model) {
            if ($relationOperation && array_key_exists($name, $requestFieldList)) {
                $relationFieldName = $name;
            }
            $fieldsInfo[$name] = $model->getFieldInfo();
        }

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('FIELDS_INFO', json_encode($fieldsInfo));
        $viewer->assign('FIELD_MODELS', $fieldList);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SMODULE', $request->get('source_module'));
        $viewer->assign('SID', $request->get('source_id'));

        echo $viewer->view('ModalAddStyle.tpl', $moduleName, true);
    }


}