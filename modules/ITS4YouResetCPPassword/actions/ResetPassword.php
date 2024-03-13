<?php

/*********************************************************************************
 * The content of this file is subject to the Reset CP Password 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

require_once("modules/Emails/mail.php");
require_once("modules/Emails/class.phpmailer.php");
require_once 'include/utils/CommonUtils.php';
require_once 'include/utils/VTCacheUtils.php';

class ITS4YouResetCPPassword_ResetPassword_Action extends Vtiger_BasicAjax_Action {

    protected $cryptmode = "";

    private function GetOutput ($mode, $E_Result)
    {
        if ($E_Result == 1)
        {
            $output['success'] = true;

            if ($mode == "send") {
                $output['message'] = vtranslate("LBL_EMAIL_OK","ITS4YouResetCPPassword");
            } else {
                $output['message'] = vtranslate("LBL_RESET_EMAIL_CONFIRMATION","ITS4YouResetCPPassword");
            }
        }
        else
        {
            $output['success'] = false;

            if ($E_Result == 2) {
                $output['message'] = vtranslate("LBL_NO_CUSTOMER_PORTAL","ITS4YouResetCPPassword");
            } else {
                $output['message'] = $E_Result;
            }
        }

        return $output;
    }

    public function process(Vtiger_Request $request) {

        $mode = "send";

        if ($request->has('mode') && !$request->isEmpty('mode')) {
            $mode = $request->get('mode');
        }

        if ($request->has('record') && !$request->isEmpty('record')) {

            $record = $request->get('record');

            $db = PearDatabase::getInstance();
            $entityData = VTEntityData::fromEntityId($db, $record);

            if ($mode == "send"){
                $E_Result = $this->sendCustomerPortalLoginDetails($entityData, true, false);
            } else {
                $E_Result = $this->CheckCustomerPortalAndEmail($entityData, false);
            }

        } else {

            $record = "";
            $E_Result = "";

        }

        $output = $this->GetOutput($mode, $E_Result);

        $result = array("success" => $output['success'], "message" => $output['message']);

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    private function CheckCustomerPortalAndEmail($entityData){

        if ($entityData->get('portal') == 'on' || $entityData->get('portal') == '1') {

            $mail = new PHPMailer(true);

            try {

                setMailerProperties($mail,'','','','',trim($entityData->get('email'),","));

                if(empty($mail->Host)) {
                    return 0;
                }

            } catch (phpmailerException $e) {
                return $e->errorMessage();
            } catch (Exception $e) {
                return $e->getMessage();
            }

            return 1;

        } else {

            return 2;

        }

    }

    private function send_mail($module,$to_email,$from_name,$from_email,$subject,$contents,$cc='',$bcc='',$attachment='',$emailid='',$logo='', $useGivenFromEmailAddress=false)
    {

        global $adb, $log;
        global $root_directory;
        global $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;

        $uploaddir = $root_directory ."/test/upload/";

        $adb->println("To id => '".$to_email."'\nSubject ==>'".$subject."'\nContents ==> '".$contents."'");

        //Get the email id of assigned_to user -- pass the value and name, name must be "user_name" or "id"(field names of vtiger_users vtiger_table)
        //$to_email = getUserEmailId('id',$assigned_user_id);

        //if module is HelpDesk then from_email will come based on support email id
        if($from_email == '') {
            //if from email is not defined, then use the useremailid as the from address
            $from_email = getUserEmailId('user_name',$from_name);
        }

        //if the newly defined from email field is set, then use this email address as the from address
        //and use the username as the reply-to address
        $cachedFromEmail = VTCacheUtils::getOutgoingMailFromEmailAddress();
        if($cachedFromEmail === null) {
            $query = "select from_email_field from vtiger_systems where server_type=?";
            $params = array('email');
            $result = $adb->pquery($query,$params);
            $from_email_field = $adb->query_result($result,0,'from_email_field');
            VTCacheUtils::setOutgoingMailFromEmailAddress($from_email_field);
        }

        if(isUserInitiated()) {
            $replyToEmail = $from_email;
        } else {
            $replyToEmail = $from_email_field;
        }
        if(isset($from_email_field) && $from_email_field!='' && !$useGivenFromEmailAddress){
            //setting from _email to the defined email address in the outgoing server configuration
            $from_email = $from_email_field;
        }

        if($module != "Calendar")
            $contents = addSignature($contents,$from_name);

        $mail = new PHPMailer(true);

        try {
            setMailerProperties($mail,$subject,$contents,$from_email,$from_name,trim($to_email,","),$attachment,$emailid,$module,$logo);
            setCCAddress($mail,'cc',$cc);
            setCCAddress($mail,'bcc',$bcc);
            if(!empty($replyToEmail)) {
                $mail->AddReplyTo($replyToEmail);
            }

            // vtmailscanner customization: If Support Reply to is defined use it.
            global $HELPDESK_SUPPORT_EMAIL_REPLY_ID;
            if($HELPDESK_SUPPORT_EMAIL_REPLY_ID && $HELPDESK_SUPPORT_EMAIL_ID != $HELPDESK_SUPPORT_EMAIL_REPLY_ID) {
                $mail->AddReplyTo($HELPDESK_SUPPORT_EMAIL_REPLY_ID);
            }
            // END

            // Fix: Return immediately if Outgoing server not configured
            if(empty($mail->Host)) {
                return 0;
            }
            // END

            $mail_status = MailSend($mail);

        } catch (phpmailerException $e) {
            return $e->errorMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $mail_status;

        if($mail_status != 1)
        {
            $mail_error = getMailError($mail,$mail_status,$mailto);
        }
        else
        {
            $mail_error = $mail_status;
        }

        return $mail_error;
    }

    private function sendCustomerPortalLoginDetails($entityData,  $SendNewPassword = true, $is_wf = true){

        $adb = PearDatabase::getInstance();
        $moduleName = $entityData->getModuleName();
        $wsId = $entityData->getId();

        $Passwords = false;

        if (!$is_wf) {
            $entityId = $wsId;
        }  else {
            $parts = explode('x', $wsId);
            $entityId = $parts[1];
        }

        $email = $entityData->get('email');

        if ($entityData->get('portal') == 'on' || $entityData->get('portal') == '1'){

            if (method_exists('Vtiger_Functions', 'generateEncryptedPassword')) {
                $this->cryptmode = "CRYPT";
            }

            $sql = "SELECT id, user_name, user_password, isactive FROM vtiger_portalinfo WHERE id=?";
            $result = $adb->pquery($sql, array($entityId));
            $insert = false;
            if($adb->num_rows($result) == 0){
                $insert = true;
            }else{
                $dbusername = $adb->query_result($result,0,'user_name');
                $isactive = $adb->query_result($result,0,'isactive');
                if($email == $dbusername && $isactive == 1 && !$entityData->isNew()){
                    $update = false;
                } else if($entityData->get('portal') == 'on' ||  $entityData->get('portal') == '1'){
                    $sql = "UPDATE vtiger_portalinfo SET user_name=?, isactive=1 WHERE id=?";
                    $adb->pquery($sql, array($email, $entityId));
                    $update = true;
                } else {
                    $sql = "UPDATE vtiger_portalinfo SET user_name=?, isactive=? WHERE id=?";
                    $adb->pquery($sql, array($email, 0, $entityId));
                    $update = false;
                }
            }

            if ($entityId == null){
                $entityId = $parts[0];
            }

            $Passwords = $this->getNewPassword();
            $password = $Passwords["send"];

            if($insert == true){

                if ($this->cryptmode == "CRYPT") {
                    $sql = "INSERT INTO vtiger_portalinfo(id,user_name,user_password,cryptmode,type,isactive) VALUES(?,?,?,?,?,?)";
                    $params = array($entityId, $email, $Passwords["save"], 'CRYPT', 'C', 1);
                } else {
                    $sql = "INSERT INTO vtiger_portalinfo(id,user_name,user_password,type,isactive) VALUES(?,?,?,?,?)";
                    $params = array($entityId, $email, $Passwords["save"], 'C', 1);
                }
                $adb->pquery($sql, $params);
            }  else {

                if ($SendNewPassword == true || $this->cryptmode == "CRYPT"){

                    if ($this->cryptmode == "CRYPT") {
                        $sql = "UPDATE vtiger_portalinfo SET user_password=?, cryptmode=? WHERE id=?";
                        $params = array($Passwords["save"], 'CRYPT', $entityId);
                    } else {
                        $sql = "UPDATE vtiger_portalinfo SET user_password=? WHERE id=?";
                        $params =  array($Passwords["save"], $entityId);
                    }

                    $adb->pquery($sql,$params);
                } else {
                    $sql = "SELECT id, user_name, user_password, isactive FROM vtiger_portalinfo WHERE id=?";
                    $result = $adb->pquery($sql, array($entityId));
                    $password = $adb->query_result($result,0,'user_password');
                }
            }
            global $current_user,$HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;
            require_once("modules/Emails/mail.php");
            $emailData = Contacts::getPortalEmailContents($entityData,$password,'LoginDetails');
            $subject = $emailData['subject'];
            $contents = $emailData['body'];
            return $this->send_mail('Contacts', $entityData->get('email'), $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $contents,'','','','','',true);

        } else {
            $sql = "UPDATE vtiger_portalinfo SET user_name=?,isactive=0 WHERE id=?";
            $adb->pquery($sql, array($email, $entityId));

            return 0;
        }

    }

    private function getNewPassword(){

        $password = makeRandomPassword();

        if ($this->cryptmode == "CRYPT"){
            $enc_password = Vtiger_Functions::generateEncryptedPassword($password);
        } else {
            $enc_password = $password;
        }

        return array("send"=>$password,"save"=>$enc_password);
    }
}
