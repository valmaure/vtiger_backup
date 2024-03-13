/*******************************************************************************
 * The content of this file is subject to the ITS4YouCreator license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ***************************************************************************** */
/** @var ITS4YouCreator_HS_Js */
jQuery.Class('ITS4YouCreator_HS_Js', {
    instance: false,
    getInstance: function () {
        if (!this.instance) {
            this.instance = new ITS4YouCreator_HS_Js();
        }

        return this.instance;
    },
}, {
    users: [],
    registerEvents: function () {
        if (this.getListView().length && (this.getListField().length || this.getListField('modifiedby').length)) {
            this.registerUsers();
            this.registerHandlers();
        }
    },
    registerUsers: function () {
        const self = this,
            params = {
                module: 'ITS4YouCreator',
                parent: 'Settings',
                action: 'Index',
                mode: 'getUsers',
                forModule: app.getModuleName(),
            };

        app.request.post({data: params}).then(function (error, data) {
            if (!error) {
                self.users = data['users']
                self.updatePicklistValues();
                self.updatePicklistValues('modifiedby');
            }
        });
    },
    getListView: function() {
        return $('#listViewContent');
    },
    getListSearchParams: function () {
        let listViewPageDiv = this.getListView(),
            searchParams = [],
            searchValue = listViewPageDiv.find('#currentSearchParams').val();

        if (searchValue) {
            searchParams = JSON.parse(searchValue);
        }

        return searchParams;
    },
    getListField: function (field = 'creator') {
        return $('[name="' + field + '"]');
    },
    updatePicklistValues: function (field = 'creator') {
        const self = this,
            creator = self.getListField(field);
        creator.html('');

        $.each(self.users, function (key, value) {
            let selected = self.isSelectedValue(field, value) ? 'selected="selected"' : '';

            creator.append('<option ' + selected + ' value="' + value + '">' + value + '</option>');
        });

        creator.trigger('change');
    },
    isSelectedValue: function (field, value) {
        const self = this,
            searchParams = self.getListSearchParams();

        if ('undefined' !== typeof searchParams[field] && 'undefined' !== typeof searchParams[field]['searchValue']) {
            let selectedUsers = searchParams[field]['searchValue'].split(',');

            return !(-1 === $.inArray(value, selectedUsers));
        }

        return false;
    },
    registerHandlers: function () {
        const self = this;

        app.event.on('post.listViewFilter.click', function () {
            self.updatePicklistValues();
            self.updatePicklistValues('modifiedby');
        });
    },
});

$(function () {
    ITS4YouCreator_HS_Js.getInstance().registerEvents();
});