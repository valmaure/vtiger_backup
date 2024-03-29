<?php
/* * *******************************************************************************
 * The content of this file is subject to the Process Flow 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Settings_ITS4YouProcessFlow_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function to get all the supported advanced filter operations
     * @return <Array>
     */
    public static function getAdvancedFilterOptions()
    {
        return array(
            'is' => 'LBL_IS',
            'contains' => 'LBL_CONTAINS',
            'does not contain' => 'LBL_DOES_NOT_CONTAIN',
            'starts with' => 'LBL_STARTS_WITH',
            'ends with' => 'LBL_ENDS_WITH',
            'is empty' => 'LBL_IS_EMPTY',
            'is not empty' => 'LBL_IS_NOT_EMPTY',
            'less than' => 'LBL_LESS_THAN',
            'greater than' => 'LBL_GREATER_THAN',
            'does not equal' => 'LBL_NOT_EQUAL_TO',
            'less than or equal to' => 'LBL_LESS_THAN_OR_EQUAL_TO',
            'greater than or equal to' => 'LBL_GREATER_THAN_OR_EQUAL_TO',
            'before' => 'LBL_BEFORE',
            'after' => 'LBL_AFTER',
            'between' => 'LBL_BETWEEN',
            'is added' => 'LBL_IS_ADDED',
            'equal to' => 'LBL_EQUALS',
            'is not' => 'LBL_IS_NOT',
            'is today' => 'LBL_IS_TODAY',
            'is tomorrow' => 'LBL_IS_TOMORROW',
            'is yesterday' => 'LBL_IS_YESTERDAY',
            'less than hours before' => 'LBL_LESS_THAN_HOURS_BEFORE',
            'less than hours later' => 'LBL_LESS_THAN_HOURS_LATER',
            'more than hours before' => 'LBL_MORE_THAN_HOURS_BEFORE',
            'more than hours later' => 'LBL_MORE_THAN_HOURS_LATER',
            'less than days ago' => 'LBL_LESS_THAN_DAYS_AGO',
            'less than days later' => 'LBL_LESS_THAN_DAYS_LATER',
            'more than days ago' => 'LBL_MORE_THAN_DAYS_AGO',
            'more than days later' => 'LBL_MORE_THAN_DAYS_LATER',
            'days ago' => 'LBL_DAYS_AGO',
            'days later' => 'LBL_DAYS_LATER',
            'in less than' => 'LBL_IN_LESS_THAN',
            'in more than' => 'LBL_IN_MORE_THAN',
        );
    }

    /**
     * Function to get the advanced filter option names by Field type
     * @return <Array>
     */
    public static function getAdvancedFilterOpsByFieldType()
    {
        return array(
            'string' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'salutation' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'text' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'url' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'email' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'phone' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'integer' => array(
                'equal to',
                'less than',
                'greater than',
                'does not equal',
                'less than or equal to',
                'greater than or equal to',
                'is field',
                'is not field',
                'is more than field',
                'is less then field',
            ),
            'double' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to'),
            'currency' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'is not empty'),
            'picklist' => array('is', 'is not', 'starts with', 'ends with', 'contains', 'does not contain', 'has changed from', 'is empty', 'is not empty'),
            'multipicklist' => array('is', 'is not', 'contains', 'does not contain'),
            'datetime' => array(
                'is',
                'is not',
                'before',
                'after',
                'is today',
                'is tomorrow',
                'is yesterday',
                'less than hours before',
                'less than hours later',
                'more than hours before',
                'more than hours later',
                'less than days ago',
                'less than days later',
                'more than days ago',
                'more than days later',
                'days ago',
                'days later',
                'is empty',
                'is not empty'
            ),
            'time' => array('is', 'is not', 'is not empty'),
            'date' => array(
                'is',
                'is not',
                'between',
                'before',
                'after',
                'is today',
                'is tomorrow',
                'is yesterday',
                'less than days ago',
                'more than days ago',
                'less than days later',
                'more than days later',
                'in less than',
                'in more than',
                'days ago',
                'days later',
                'is empty',
                'is not empty',
                'is field',
                'is not field',
                'is more than field',
                'is less then field',
            ),
            'boolean' => array('is', 'is not'),
            'reference' => array('is empty', 'is not empty'),
            'multireference' => array(),
            'owner' => array('is', 'is not', 'is empty', 'is not empty'),
            'ownergroup' => array('is', 'is not'),
            'recurrence' => array('is', 'is not'),
            'comment' => array('is'),
            'image' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
            'percentage' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'is not empty'),
            'currencyList' => array('is', 'is not'),
            'userRole' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with'),
        );
    }

    /**
     * Function to get comment field which will useful in creating conditions
     * @param <Vtiger_Module_Model> $moduleModel
     * @return <Vtiger_Field_Model>
     */
    public static function getCommentFieldForFilterConditions($moduleModel)
    {
        $commentField = new Vtiger_Field_Model();
        $commentField->set('name', '_VT_add_comment');
        $commentField->set('label', 'Comment');
        $commentField->setModule($moduleModel);
        $commentField->fieldDataType = 'comment';

        return $commentField;
    }

    /**
     * Function to get comment fields list which are useful in tasks
     * @param <Vtiger_Module_Model> $moduleModel
     * @return <Array> list of Field models <Vtiger_Field_Model>
     */
    public static function getCommentFieldsListForTasks($moduleModel)
    {
        $commentsFieldsInfo = array('lastComment' => 'Last Comment', 'last5Comments' => 'Last 5 Comments', 'allComments' => 'All Comments');

        $commentFieldModelsList = array();
        foreach ($commentsFieldsInfo as $fieldName => $fieldLabel) {
            $commentField = new Vtiger_Field_Model();
            $commentField->setModule($moduleModel);
            $commentField->set('name', $fieldName);
            $commentField->set('label', $fieldLabel);
            $commentFieldModelsList[$fieldName] = $commentField;
        }
        return $commentFieldModelsList;
    }
}
