/*********************************************************************************
 * The content of this file is subject to the Reset CP Password 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

ITS4YouResetCPPassword_Detail_Js = {
    ResetPassword: function (record) {
        let message,
            params = {
                module: 'ITS4YouResetCPPassword',
                action: 'ResetPassword',
                record: record,
                mode: 'control',
            };

        app.request.post({data: params}).then(function (error, data) {
                if (!error) {
                    if (data.success) {
                        message = data.message;

                        app.helper.showConfirmationBox({'message': message}).then(function (data) {
                            params['mode'] = 'send';

                            app.request.post({data: params}).then(function (error, data) {
                                    if (!error) {
                                        if (data.success) {
                                            app.helper.showAlertBox({'message': data.message}).then(function (data) {
                                            });
                                        }
                                    }
                                },
                            )
                        })
                    } else {
                        if (!data.message) {
                            message = app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION');
                        }

                        app.helper.showAlertBox({'message': message}).then(function (data) {
                        });
                    }
                }
            },
        )
    },
};
