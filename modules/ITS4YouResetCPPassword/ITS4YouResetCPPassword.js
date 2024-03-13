/*********************************************************************************
 * The content of this file is subject to the Reset CP Password 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

ITS4YouResetCPPassword_Detail_Js = {
    
    ResetPassword : function(record) {                                                                                                                        
                                          
        var message;
        var action_url = 'index.php?module=ITS4YouResetCPPassword&action=ResetPassword&record='+record;
        
        AppConnector.request(action_url + '&mode=control').then(function(data){
          if(data.result.success == true){
            message = data.result.message;
            
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data){                              
                                                                                                                      
                AppConnector.request(action_url).then(function(data){                                                                                          
                                                                                                                    
    					   if(data.success == true){                                                                              
                                                                                                                      
                                if(data.result.success == true){ 
                                    var message_type  =  "success";
                                }  else {
                                    var message_type  =  "error";
                                }     
                                                                                                             
                                var params = {                                                                        
                                    text: data.result.message,                                       
                                    type: message_type                                                                     
                                };                                                                                    
                                                                                                                      
                                Vtiger_Helper_Js.showMessage(params);  
                            }                                                          
				        });                                                                                                           
        },                                                                                                        
			  function(error, err){                                                                                          
			  });
            
          } else { 
            if (data.result.message == 0) {
              data.result.message = app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION');
            }
            var params = {                                                                        
                text: data.result.message,                                       
                type: "error"                                                                     
            };
            Vtiger_Helper_Js.showMessage(params);
          }
        });
                                                                                                                                                                                                           
                                                                                                                                                       
	},           
};
