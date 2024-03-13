<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ITS4YouStyles_Save_Action extends Vtiger_Save_Action
{

    public function process(Vtiger_Request $request)
    {

        $mode = "";
        if ($request->has('mode') && !$request->isEmpty('mode')) {
            $mode = $request->get('mode');
        }

        $recordModel = $this->saveRecord($request);
        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentRecordId = $request->get('sourceRecord');

            if (substr($parentRecordId, 0, 1) == "t") {
                $parentRecordId = substr($parentRecordId, 1);
                $loadUrl = "index.php?module=" . $parentModuleName . "&view=Detail&record=" . $parentRecordId . "&relatedModule=ITS4YouStyles&mode=showRelatedList&tab_label=" . vtranslate("LBL_STYLES_LIST", "ITS4YouStyles");
            } else {
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
                //TODO : Url should load the related list instead of detail view of record
                $loadUrl = $parentRecordModel->getDetailViewUrl();
            }
        } else {
            if ($request->get('returnToList')) {
                $loadUrl = $recordModel->getModule()->getListViewUrl();
            } else {
                $loadUrl = $recordModel->getDetailViewUrl();
            }
        }

        if ($mode == "Ajax") {
            $response = new Vtiger_Response();
            $response->setResult(array());
            $response->emit();
        } else {
            header("Location: $loadUrl");
        }
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordModel->save();

        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            if ($parentModuleName == "PDFMaker" || $parentModuleName == "EMAILMaker") {
                $adb = PearDatabase::getInstance();
                $adb->pquery("insert into its4you_stylesrel (styleid, parentid, module) values(?,?,?)", array($relatedRecordId, $parentRecordId, $parentModuleName));
            } else {
                $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
                $relationModel->addRelation($parentRecordId, $relatedRecordId);
            }
        }
        if ($request->get('imgDeleted')) {
            $imageIds = $request->get('imageid');
            foreach ($imageIds as $imageId) {
                $status = $recordModel->deleteImage($imageId);
            }
        }
        return $recordModel;
    }
}
