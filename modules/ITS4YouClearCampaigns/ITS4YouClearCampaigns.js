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
			this.detailViewContentHolder = jQuery('div.details div.contents');
		}
		return this.detailViewContentHolder;
	},

	getSelectedTab : function() {
		var tabContainer = this.getTabContainer();
		return tabContainer.find('li.active');
	},

	getTabContainer : function(){
		return jQuery('div.related');
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

		AppConnector.request(action_url + '&mode=control').then(function(data){

			if(data.result.success == true){

				var message = data.result.message;

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


							var selectedTabElement = thisInstance.getSelectedTab();
							var relatedModuleName = thisInstance.getRelatedModuleName();
							selectedTabLabelKey=selectedTabElement.data("label-key");

							if (selectedTabLabelKey == list) {
								var relatedController = new Campaigns_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
								relatedController.loadRelatedList();
							}


						});
					},
					function(error, err){
					});

			} else {

				var params = {
					text: data.result.message,
					type: "error"
				};
				Vtiger_Helper_Js.showMessage(params);

			}

		});

	},
};
