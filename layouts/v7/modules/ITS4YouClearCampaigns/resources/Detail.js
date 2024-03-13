/*********************************************************************************
 * The content of this file is subject to the Clear Campaigns 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

ITS4YouClearCampaigns_Detail_Js = {
    
    detailViewContentHolder : false,
    
    getContentHolder : function() {
		if(this.detailViewContentHolder == false) {
			this.detailViewContentHolder = jQuery('div.details, div.contents');
		}
		return this.detailViewContentHolder;
	},
    
    getSelectedTab : function() {
		var tabContainer = this.getTabContainer();
		return tabContainer.find('li.active');
	},
    
    getTabContainer : function(){
		return jQuery('div.related-tabs');
	},
    
    getRelatedModuleName : function() {
		return jQuery('.relatedModuleName',this.getContentHolder()).val();
	},
   
    getRecordId : function(){
		return jQuery('#recordId').val();
	},
    
    ClearCampaign : function(campaign,list) {                                                                                                                        
                                          
        var thisInstance = this;
        var action_url = 'index.php?module=ITS4YouClearCampaigns&action=ListDelete&campaign='+campaign+'&list='+list;
		var aDeferred = jQuery.Deferred();

        /*.open(action_url  + '&mode=control');*/
		app.request.get({'url':action_url  + '&mode=control'}).then(
			function(error, data){
				if(error == null) {
					//alert(JSON.stringify(data));
					if(data.success == true) {
						var message = data.message;
						app.helper.showConfirmationBox({'message' : message}).then(function(data){
							app.request.get({'url':action_url}).then(
								function(error, data){
									if(error == null) {
										//alert(JSON.stringify(data));
										if(data.success == true) {
											var message = data.message;
											var selectedTabElement = thisInstance.getSelectedTab();
											var relatedModuleName = thisInstance.getRelatedModuleName();
											var selectedTabLabelKey = selectedTabElement.data("label-key");

											if (selectedTabLabelKey == list) {
												var relatedController = new Campaigns_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
												relatedController.loadRelatedList();
											}
											
											app.helper.showAlertBox({'message' : message}).then(function(data){

											});
										}
									}
								},
								function(error){
									aDeferred.reject();
								}
							)
						})
					}
				}
			},
			function(error){
				aDeferred.reject();
			}
		)
    },           
};