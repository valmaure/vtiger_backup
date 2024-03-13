<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class ITS4YouStyles_Record_Model extends Vtiger_Record_Model
{

    public function save()
    {
        parent::save();

        $id = $this->getId();
        $data = $this->getData();

        $saveasfile = "modules/ITS4YouStyles/resources/files/style_" . $id . ".css";
        $fh = fopen($saveasfile, 'w');
        fwrite($fh, $data["stylecontent"]);
        fclose($fh);
    }
}
