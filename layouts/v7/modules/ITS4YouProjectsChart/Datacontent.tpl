{*<!--
/*+***********************************************************************************
 * The content of this file is subject to the ITS4YouProjectsChart license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
*************************************************************************************/
-->*}
{strip}
    {assign var="PROJECT_MODEL" value=Vtiger_Module_Model::getInstance('Project')}
    {if !empty($PROJECTS['tasks'])}
        <br/>
        <input id="projects" type="hidden" value="{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($PROJECTS))}">
        <input id="originalprojects" type="hidden" value="{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($PROJECTS))}">
        <input id="userDateFormat" type="hidden" value="{$USER_DATE_FORMAT}">
        <div id="workSpace" style="height:50em;padding:0px;border:1px solid #e5e5e5;position:relative;margin:0 5px"></div>
        <div id="gantEditorTemplates" style="display:none;">
            <div class="__template__" type="TASKSEDITHEAD"><!--
                <table class="gdfTable" cellspacing="0" cellpadding="0">
                  <thead>
                  <tr style='height:50px'>
                    <th class="gdfColHeader" style="width:35px;"></th>
                    {*
                    <th class="gdfColHeader cursorPointer" style="width:80px;text-align:center;" data-name="status_value" data-text="{vtranslate('LBL_STATUS', $PROJECT_MODULE)}">{vtranslate('LBL_STATUS', $PROJECT_MODULE)}</th>
                    *}
                    <th class="gdfColHeader cursorPointer" style="width:240px;text-align:center;" data-name="name" data-text="{vtranslate('Project Name', $PROJECT_MODULE)}">{vtranslate('Project Name', $PROJECT_MODULE)}</th>
                    <th class="gdfColHeader cursorPointer" style="width:90px;text-align:center;" data-name="startdate" data-text="{vtranslate('LBL_START_DATE', $PROJECT_MODULE)}" >{vtranslate('LBL_START_DATE', $PROJECT_MODULE)}</th>
                    <th class="gdfColHeader cursorPointer" style="width:90px;text-align:center;" data-name="enddate" data-text="{vtranslate('LBL_END_DATE', $PROJECT_MODULE)}" >{vtranslate('LBL_END_DATE', $PROJECT_MODULE)}</th>
                    {*
                    <th class="gdfColHeader cursorPointer" style="width:40px;text-align:center;" data-name="duration" data-text="{vtranslate('LBL_DURATION', $PROJECT_MODULE)}" title="{vtranslate('LBL_DURATION', $PROJECT_MODULE)}">D</th>
                    *}
                  </tr>
                  </thead>
                </table>
                --></div>

            <div class="__template__" type="TASKROW"><!--
                <tr taskId="(#=obj.id#)" class="taskEditRow" level="(#=level#)">
                  <th class="gdfCell editTaskDIS" align="right" style="text-align:center;" data-recordid="(#=obj.recordid#)"><span class="taskRowIndex">(#=obj.getRow()+1#)</span> {*<span class="teamworkIcon" style="font-size:12px;" >e</span>*}</th>
                  {*
                  <td class="gdfCell" name="status_value">(#=obj.status_value#)</td>
                  *}
                  <td class="gdfCell indentCell" style="padding-left:(#=obj.level*10#)px;">
                    <div class="taskStatus cvcColorSquare" status="(#=obj.number#)"></div>&nbsp;
                    <div class="(#=obj.isParent()?'exp-controller expcoll exp':'exp-controller'#)" align="center"></div>
                    <a href="index.php?module={$PROJECT_MODULE}&view={$PROJECT_MODEL->getDetailViewName()}&record=(#=obj.recordid#)">
                    (#=obj.name#)
                    </a>
                  </td>

                  <td class="gdfCell" name="start"></td>
                  <td class="gdfCell" name="end"></td>
                  {*
                  <td class="gdfCell" name="durationtext" style="text-align:center;">(#=obj.duration#)</td>
                  *}
                </tr>
                --></div>

            <div class="__template__" type="TASKEMPTYROW"><!--
                <tr class="taskEditRow emptyRow" >
                  <th class="gdfCell" align="right"></th>
                  <td class="gdfCell noClip" align="center"></td>
                  {*
                  <td class="gdfCell"></td>
                  *}
                  <td class="gdfCell"></td>
                  <td class="gdfCell"></td>
                  <td class="gdfCell"></td>
                  {*
                  <td class="gdfCell"></td>
                  *}
                </tr>
                --></div>

            <div class="__template__" type="TASKBAR"><!--
                <div class="taskBox taskBoxDiv" taskId="(#=obj.id#)" >
                  <div class="layout (#=obj.hasExternalDep?'extDep':''#)">
                    <div class="taskStatus" status="(#=obj.number#)"></div>
                    <div></div>
                    <div class="taskProgress" style="width:(#=obj.progress>100?100:obj.progress#)%; background-color:(#=obj.progress>100?'red':'rgb(153,255,51);'#);"></div>
                    <div class="milestone (#=obj.startIsMilestone?'active':''#)" ></div>

                    <div class="taskLabel"></div>
                    <div class="milestone end (#=obj.endIsMilestone?'active':''#)" ></div>
                  </div>
                </div>
                --></div>

            <div class="__template__" type="CHANGE_STATUS"><!--
                  <div class="taskStatusBox">
                    <div class="taskStatus cvcColorSquare" status="STATUS_ACTIVE" title="active"></div>
                    <div class="taskStatus cvcColorSquare" status="STATUS_DONE" title="completed"></div>
                    <div class="taskStatus cvcColorSquare" status="STATUS_FAILED" title="failed"></div>
                    <div class="taskStatus cvcColorSquare" status="STATUS_SUSPENDED" title="suspended"></div>
                    <div class="taskStatus cvcColorSquare" status="STATUS_UNDEFINED" title="undefined"></div>
                  </div>
                --></div>


            <div class="__template__" type="TASK_EDITOR"><!--
                <div class="ganttTaskEditor">
                <table width="100%">
                  <tr>
                    <td>
                      <table cellpadding="5">
                        <tr>
                          <td><label for="code">code/short name</label><br><input type="text" name="code" id="code" value="" class="formElements"></td>
                         </tr><tr>
                          <td><label for="name">name</label><br><input type="text" name="name" id="name" value=""  size="35" class="formElements"></td>
                        </tr>
                        <tr></tr>
                          <td>
                            <label for="description">description</label><br>
                            <textarea rows="5" cols="30" id="description" name="description" class="formElements"></textarea>
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td valign="top">
                      <table cellpadding="5">
                        <tr>
                        <td colspan="2"><label for="status">status</label><br><div id="status" class="taskStatus" status=""></div></td>
                        <tr>
                        <td colspan="2"><label for="progress">progress</label><br><input type="text" name="progress" id="progress" value="" size="3" class="formElements"></td>
                        </tr>
                        <tr>
                        <td><label for="start">start</label><br><input type="text" name="start" id="start"  value="" class="date" size="10" class="formElements"><input type="checkbox" id="startIsMilestone"> </td>
                        <td rowspan="2" class="graph" style="padding-left:50px"><label for="duration">dur.</label><br><input type="text" name="duration" id="duration" value=""  size="5" class="formElements"></td>
                      </tr><tr>
                        <td><label for="end">end</label><br><input type="text" name="end" id="end" value="" class="date"  size="10" class="formElements"><input type="checkbox" id="endIsMilestone"></td>
                      </table>
                    </td>
                  </tr>
                  </table>

                <h2>assignments</h2>
                <table  cellspacing="1" cellpadding="0" width="100%" id="assigsTable">
                  <tr>
                    <th style="width:100px;">name</th>
                    <th style="width:70px;">role</th>
                    <th style="width:30px;">est.wklg.</th>
                    <th style="width:30px;" id="addAssig"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
                  </tr>
                </table>

                <div style="text-align: right; padding-top: 20px"><button id="saveButton" class="button big">save</button></div>
                </div>
                --></div>


            <div class="__template__" type="ASSIGNMENT_ROW"><!--
                <tr taskId="(#=obj.task.id#)" assigId="(#=obj.assig.id#)" class="assigEditRow" >
                  <td ><select name="resourceId"  class="formElements" (#=obj.assig.id.indexOf("tmp_")==0?"":"disabled"#) ></select></td>
                  <td ><select type="select" name="roleId"  class="formElements"></select></td>
                  <td ><input type="text" name="effort" value="(#=getMillisInHoursMinutes(obj.assig.effort)#)" size="5" class="formElements"></td>
                  <td align="center"><span class="teamworkIcon delAssig" style="cursor: pointer">d</span></td>
                </tr>
                --></div>


            <div class="__template__" type="RESOURCE_EDITOR"><!--
                <div class="resourceEditor" style="padding: 5px;">

                  <h2>Project team</h2>
                  <table  cellspacing="1" cellpadding="0" width="100%" id="resourcesTable">
                    <tr>
                      <th style="width:100px;">name</th>
                      <th style="width:30px;" id="addResource"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
                    </tr>
                  </table>

                  <div style="text-align: right; padding-top: 20px"><button id="resSaveButton" class="button big">save</button></div>
                </div>
                --></div>


            <div class="__template__" type="RESOURCE_ROW"><!--
                <tr resId="(#=obj.id#)" class="resRow" >
                  <td ><input type="text" name="name" value="(#=obj.name#)" style="width:100%;" class="formElements"></td>
                  <td align="center"><span class="teamworkIcon delRes" style="cursor: pointer">d</span></td>
                </tr>
                --></div>
        </div>
        {*
            <div class="row" style="min-height:300px;margin-top: 10px; padding: 5px;">
                <div class="col-lg-4">
                    <table class="table table-condensed table-striped table-bordered ">
                        <thead>
                            <tr>
                                <td></td>
                                <td><b>{vtranslate('LBL_STATUS', $PROJECT_MODULE)}</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$TASK_STATUS item=STATUS_NAME}
                                {assign var=STATUS_NAME value=trim($STATUS_NAME)}
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-3"> &nbsp; </div>
                                            <div class="col-lg-3">
                                                <div status="{Project_Record_Model::getGanttStatus($STATUS_NAME)}" class="taskStatus cvcColorSquare"></div>
                                            </div>
                                            {if $STATUS_FIELD_MODEL->isEditable()}
                                                <div class="col-lg-3">
                                                    <a onclick="javascript:ITS4YouProjectsChart_Chart_Js.showEditColorModel('index.php?module={$MODULE}&view=EditAjax&mode=editColor&status={$STATUS_NAME}', this)" data-status="{$STATUS_NAME}" data-color="{$TASK_STATUS_COLOR[$STATUS_NAME]}"><i title="{vtranslate('LBL_EDIT_COLOR', $PROJECT_MODULE)}" class="fa fa-pencil alignMiddle"></i></a>&nbsp;
                                                </div>
                                            {/if}
                                        </div>
                                    </td>
                                    <td>{vtranslate($STATUS_NAME,'Project')}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8">
                    <div style="position: relative;width:93%" class="row alert-info well">
                        <span class="span alert-info">
                            <span style="padding: 1%"><b>{vtranslate('LBL_INFO',$PROJECT_MODULE)}</b></span>
                            <ul>
                                <li>{vtranslate('LBL_GANTT_INFO1', $PROJECT_MODULE)}</li>
                                <li>{vtranslate('LBL_GANTT_INFO2', $PROJECT_MODULE)}</li>
                            </ul>
                        </span>
                    </div>
                </div>
        </div>
        *}

    {else}
        <table class="emptyRecordsDiv">
            <tbody>
            <tr>
                <td>
                    {assign var="IS_MODULE_EDITABLE" value=$PROJECT_MODEL->isPermitted('CreateView')}
                    {assign var=SINGLE_MODULE value="SINGLE_Project"}
                    {vtranslate('LBL_NO')} {vtranslate('Project', 'Project')} {vtranslate('LBL_FOUND')} {vtranslate('LBL_NO_RECORDS_FOUND', 'Project')}.{if $IS_MODULE_EDITABLE} <a
                        href="{$PROJECT_MODEL->getCreateRecordUrl()}"> {vtranslate('LBL_CREATE')} </a>{/if}
                </td>
            </tr>
            </tbody>
        </table>
    {/if}
{/strip}