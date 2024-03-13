<?php

/*********************************************************************************
 * The content of this file is subject to the Clear Campaigns 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/


class ITS4YouClearCampaigns_ListDelete_Action extends Vtiger_BasicAjax_Action {

    protected $sourceRecordId = "";
    protected $relatedModuleName = "";
    
    private $parentRecordModel = false;
    
    public function GetOutput ($mode, $E_Result)
    {
        $output['message'] = false;        
        $output['success'] = $E_Result;
                                                     
        if ($mode == "delete" && !$E_Result) {
            $output['message'] = vtranslate("LBL_DELETE_ERROR","ITS4YouClearCampaigns");
        }     
     
        if (!$output['message']) {$output['message'] = vtranslate(($E_Result?"LBL_DELETE_":"LBL_NO_").strtoupper($this->relatedModuleName).($E_Result?($mode == "delete"?"":"_CONFIRMATION"):""),"ITS4YouClearCampaigns");}
        
        return $output;
        
    }
    
    public function process(Vtiger_Request $request) {
    

        $mode = "delete";
        
        if ($request->has('mode') && !$request->isEmpty('mode')) { 
            $mode = $request->get('mode'); 
        }
        
        if ($request->has('campaign') && !$request->isEmpty('campaign') && $request->has('list') && !$request->isEmpty('list')) {
        
            $this->sourceRecordId = $request->get('campaign');
            $this->relatedModuleName = $request->get('list');
            
            if ($mode == "delete"){
                $E_Result = $this->DeleteList();   
            } else {
                $E_Result = $this->CheckList();
            }
            
        } else {
        
            $E_Result = "";
            
        }                
        
        $output = $this->GetOutput($mode, $E_Result);
        
        $result = array("success" => $output['success'], "message" => $output['message']);
        
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
         
    }

    function RelationListView () {
        
        if ($this->parentRecordModel == false) {
            $this->parentRecordModel = Vtiger_Record_Model::getInstanceById($this->sourceRecordId, 'Campaigns');    		
        }
        return Vtiger_RelationListView_Model::getInstance($this->parentRecordModel, $this->relatedModuleName);
        
    }
    
    function CheckList () {
        
        $relationListView = $this->RelationListView();
        
        $totalCount = $relationListView->getRelatedEntriesCount();
        
        if ($totalCount > 0){
            return true;
        } else {
            return false;
        }
        
    }
    
    function getRelatedRecordList(){
    
        $relatedRecordIdList = array();
        
        $relationListView = $this->RelationListView(); 
        
        $query = $relationListView->getRelationQuery();
        
        $db = PearDatabase::getInstance();
        $result = $db->pquery($query, array());
        
        for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
            $recordId = $db->query_result($result,$i,'crmid');
            
            $relatedRecordIdList[] =  $recordId;
        }
          
        return $relatedRecordIdList;
    }   
    
    function DeleteList() {            
        
        $sourceModuleModel = Vtiger_Module_Model::getInstance('Campaigns');
		$relatedModuleModel = Vtiger_Module_Model::getInstance($this->relatedModuleName);
		$relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
		
        
        $relatedRecordIdList = $this->getRelatedRecordList($this->sourceRecordId,$this->relatedModuleName);
        
        foreach($relatedRecordIdList as $relatedRecordId) {
			$response = $relationModel->deleteRelation($this->sourceRecordId,$relatedRecordId);
		}

        return !$this->CheckList($this->sourceRecordId, $this->relatedModuleName);
         
              
    }
    
    
    
}