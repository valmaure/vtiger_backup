<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class ITS4YouStyles extends Vtiger_CRMEntity {
	public $table_name = 'its4you_styles';
	public $table_index= 'styleid';
	public $moduleName = 'ITS4YouStyles';
	public $parentName = 'Tools';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = Array('its4you_stylescf', 'styleid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = Array('vtiger_crmentity', 'its4you_styles', 'its4you_stylescf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'its4you_styles' => 'styleid',
		'its4you_stylescf'=>'styleid');

    /**
     * @var array [<fieldlabel> => array(<tablename> => <columnname>)]
     */
    public $list_fields = array(
        'Assigned To' => array('crmentity' => 'smownerid'),
        'Description' => array('crmentity' => 'description'),
    );

    /**
     * @var array [<fieldlabel> => <fieldname>]
     */
    public $list_fields_name = array(
        'Assigned To' => 'assigned_user_id',
        'Description' => 'description',
    );

	// Make the field link to detail view
	public $list_link_field = 'stylename';

	// For Popup listview and UI type support
	public $search_fields = Array(
	);
	public $search_fields_name = Array (
	);

	// For Popup window record selection
	public $popup_fields = Array ('');

	// For Alphabetical search
	public $def_basicsearch_col = '';

	// Column value to use on detail view record text display
	public $def_detailview_recname = '';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = Array('','assigned_user_id');

	public $default_order_by = '';
	public $default_sort_order='ASC';

	function ITS4YouActivityManager() {
	    global $log;

        $this->log = $log;
        $this->db = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('ITS4YouStyles');
	}

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
    function vtlib_handler($moduleName, $eventType)
    {
        include_once 'vtlib/Vtiger/Module.php';
        require_once 'modules/ModComments/ModComments.php';
        require_once 'modules/ModTracker/ModTracker.php';

        switch ($eventType) {
            case 'module.postinstall':
            case 'module.enabled':
            case 'module.postupdate':
                $this->addCustomLinks();
                break;
            case 'module.disabled':
            case 'module.preupdate':
            case 'module.preuninstall':
                $this->deleteCustomLinks();
                break;
        }
    }

    public function addCustomLinks()
    {
        Settings_MenuEditor_Module_Model::addModuleToApp($this->moduleName, $this->parentName);
        ModComments::addWidgetTo([$this->moduleName]);
        ModTracker::enableTrackingForModule(getTabid($this->moduleName));
    }

    public function deleteCustomLinks()
    {
        ModComments::removeWidgetFrom([$this->moduleName]);
        ModTracker::disableTrackingForModule(getTabid($this->moduleName));
    }
	
        function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

                if (substr($return_id,0, 1) == "t"){
                        $return_id = substr($return_id, 1);
			$sql = 'DELETE FROM its4you_stylesrel WHERE styleid =? AND parentid = ? AND module = ?';
			$this->db->pquery($sql, array($id, $return_id, $return_module));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = $this->db;
		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if (substr($with_crmid,0, 1) == "t"){
                                $with_crmid = substr($with_crmid, 1);
				$adb->pquery("insert into its4you_stylesrel (styleid, parentid, module) values(?,?,?)", array($crmid, $with_crmid, $with_module));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}
        
        /*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables =  array (
			"EMAILMaker" => array("its4you_stylesrel"=>array("styleid","parentid"),"vtiger_emakertemplates"=>"templateid"),
                        "PDFMaker" => array("its4you_stylesrel"=>array("styleid","parentid"),"vtiger_pdfmaker"=>"templateid"),
                );
		return $rel_tables[$secmodule];
	}
}
