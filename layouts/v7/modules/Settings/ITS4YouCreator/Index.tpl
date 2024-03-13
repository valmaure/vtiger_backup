{*<!--
/*********************************************************************************
* The content of this file is subject to the ITS4YouCreator
* ("License"); You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
* Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
* All Rights Reserved.
********************************************************************************/
-->*}
{strip}
    <div id="Settings_{$MODULE_NAME}_Index_View">
        <div class="listViewContentDiv col-lg-12">
            <h4>{vtranslate('LBL_MODULE_NAME', $QUALIFIED_MODULE)} {vtranslate('LBL_SETTINGS', $QUALIFIED_MODULE)}</h4>
            <h6>{vtranslate('LBL_CREATOR_DESCRIPTION', $QUALIFIED_MODULE)}</h6>
            <hr>
            <form name="linkModulesForm" id="linkModulesForm" method="POST">
                <table class="table table-bordered equalSplit">
                    <tr>
                        <th>{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</th>
                        <th style="width: 150px;">{vtranslate('LBL_CREATOR_FIELD', $QUALIFIED_MODULE)}</th>
                        <th style="width: 150px;">{vtranslate('LBL_MODIFIED_BY_FIELD', $QUALIFIED_MODULE)}</th>
                        <th>{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</th>
                        <th style="width: 150px;">{vtranslate('LBL_CREATOR_FIELD', $QUALIFIED_MODULE)}</th>
                        <th style="width: 150px;">{vtranslate('LBL_MODIFIED_BY_FIELD', $QUALIFIED_MODULE)}</th>
                    </tr>
                    {assign var=COUNTER value=0}
                    <tr>
                        {foreach item=MODULE from=$ALL_MODULES}
                        {assign var=MODULE_ID value=$MODULE->id}
                        {assign var=MODULE_NAME value=$MODULE->name}
                        {assign var=MODULE_LABEL value=vtranslate($MODULE->label, $MODULE_NAME)}
                        {assign var=CREATOR_ACTIVE value=$ALL_FIELDS[$MODULE_ID]['creator_active']}
                        {assign var=MODIFIED_BY_ACTIVE value=$ALL_FIELDS[$MODULE_ID]['modified_by_active']}
                        {if $COUNTER eq 2}
                            {assign var=COUNTER value=0}
                            </tr><tr>
                        {/if}
                        <td style="line-height: 1;">
                            {$MODULE_LABEL}
                        </td>
                        <td style="line-height: 1;">
                            <input type="checkbox" class='its4you_field_checkbox' data-field="creator" data-tab_id="{$MODULE_ID}" data-module="{$MODULE_NAME}" {if $CREATOR_ACTIVE}checked{/if} />
                        </td>
                        <td style="line-height: 1;">
                            <input type="checkbox" class='its4you_field_checkbox' data-field="modifiedby" data-tab_id="{$MODULE_ID}" data-module="{$MODULE_NAME}" {if $MODIFIED_BY_ACTIVE}checked{/if} />
                        </td>
                        {assign var=COUNTER value=$COUNTER+1}
                        {/foreach}
                    </tr>
                </table>
            </form>
        </div>
    </div>
{/strip}