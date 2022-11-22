<?php

/**
 * Case search task: Create CiviOffice documents for selected case.
 */
class CRM_Civioffice_Form_Task_CreateCaseDocuments extends CRM_Case_Form_Task
{
    use CRM_Civioffice_Form_Task_CreateDocumentsTrait;

    public function preProcess()
    {
        parent::preProcess();
        $this->entityType = 'case';
        $this->entityIds = $this->_entityIds;
    }

    /**
     * @param $caseId
     *
     * @return null|int
     */
    public static function getCaseClientContactId($caseId) {
        if (empty($caseId)) {
            return NULL;
        }

        $caseContact = \Civi\Api4\CaseContact::get()
            ->addSelect('contact_id')
            ->addWhere('case_id', '=', $caseId)
            ->execute()
            ->first();

        if (empty($caseContact)) {
            return NULL;
        }

        return $caseContact['contact_id'];
    }

}
