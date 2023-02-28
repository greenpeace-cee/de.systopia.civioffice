<?php

/**
 * Case search task: Create CiviOffice documents for selected activity.
 */
class CRM_Civioffice_Form_Task_CreateActivityDocuments extends CRM_Case_Form_Task
{
    use CRM_Civioffice_Form_Task_CreateDocumentsTrait;

    public function preProcess()
    {
        parent::preProcess();
        $this->entityType = 'activity';
        $this->entityIds = $this->_entityIds;
    }

    /**
     * @param $activityId
     *
     * @return null|int
     */
    public static function getActivityContactId($activityId) {
        if (empty($activityId)) {
            return NULL;
        }

        $activityContact = \Civi\Api4\ActivityContact::get()
            ->addWhere('activity_id', '=', $activityId)
            ->addWhere('record_type_id:name', '=', 'Activity Assignees')
            ->setLimit(1)
            ->execute()
            ->first();

        if (empty($activityContact)) {
            return NULL;
        }

        return $activityContact['contact_id'];
    }

    /**
     * @param $activityId
     *
     * @return null|int
     */
    public static function getActivityCaseId($activityId) {
        if (empty($activityId)) {
            return NULL;
        }

        $caseActivity = \Civi\Api4\CaseActivity::get()
            ->addSelect('case_id')
            ->addWhere('activity_id', '=', $activityId)
            ->setLimit(1)
            ->execute()
            ->first();

        if (empty($caseActivity)) {
            return NULL;
        }

        return $caseActivity['case_id'];
    }

}
