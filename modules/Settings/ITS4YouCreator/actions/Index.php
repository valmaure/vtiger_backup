<?php
/*******************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ***************************************************************************** */

class Settings_ITS4YouCreator_Index_Action extends Vtiger_BasicAjax_Action
{

    public function __construct()
    {
        parent::__construct();

        $this->exposeMethod('updateField');
        $this->exposeMethod('getUsers');
    }

    /**
     * @param Vtiger_Request $request
     * @return void
     * @throws Exception
     */
    public function process(Vtiger_Request $request)
    {

        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }

        $this->updateField($request);
    }

    public function updateField(Vtiger_Request $request)
    {
        $qualifiedModule = $request->getModule(false);
        $tabId = $request->get('tab_id');
        $mode = $request->get('field_mode');
        $field = $request->get('field');
        $result = ITS4YouCreator_Module_Model::updateModuleField($tabId, $mode, $field);

        $return = array('success' => $result, 'message' => vtranslate('LBL_FIELD_UPDATED', $qualifiedModule));
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    public function getUsers(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $users = $currentUser->getAccessibleUsers('', $request->get('forModule'));
        $response = new Vtiger_Response();
        $response->setResult(array(
            'users' => $users
        ));
        $response->emit();
    }
}