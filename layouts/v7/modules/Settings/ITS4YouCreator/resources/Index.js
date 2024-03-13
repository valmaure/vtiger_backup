/*******************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ***************************************************************************** */
/** @var Settings_ITS4YouCreator_Index_Js */
Settings_Vtiger_Index_Js('Settings_ITS4YouCreator_Index_Js', {}, {
    updateModuleStatus: function(tab_id, mode, field) {
        let message = app.vtranslate('JS_UPDATE_FIELD'),
            params = {
                data: {
                    module: 'ITS4YouCreator',
                    parent: 'Settings',
                    action: 'Index',
                    tab_id: tab_id,
                    mode: 'updateField',
                    field_mode: mode,
                    field: field,
                }
            };

        app.request.post(params).then(function(error, data) {

            if(error === null) {
                if(data.message) {
                    message = data.message;
                }

                app.helper.showSuccessNotification({message: message});
            }
        });
    },
    registerClickEvent: function() {
        let self = this;

        $('.its4you_field_checkbox').on('click', function() {
            let thisCheckbox = jQuery(this),
                data = thisCheckbox.data(),
                checked = thisCheckbox.attr('checked'),
                mode = 'Hide';

            if(checked === 'checked') {
                mode = 'Show';
            }

            self.updateModuleStatus(data.tab_id, mode, data.field);
        });
    },
    registerEvents: function() {
        this._super();
        this.registerClickEvent();
    }
});