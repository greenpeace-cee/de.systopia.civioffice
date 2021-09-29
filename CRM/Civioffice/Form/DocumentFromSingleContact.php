<?php
/*-------------------------------------------------------+
| SYSTOPIA CiviOffice Integration                        |
| Copyright (C) 2021 SYSTOPIA                            |
| Author: J. Franz (franz@systopia.de)                   |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*/

use CRM_Civioffice_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Civioffice_Form_DocumentFromSingleContact extends CRM_Core_Form {

    /** @var integer contact ID */
    public $contact_id = null;

    const UNOCONV_CREATE_SINGLE_ACTIVIY_TYPE = 'civioffice_create_single_activity_type';
    const UNOCONV_CREATE_SINGLE_ACTIVIY_ATTACHMENT = 'civioffice_create_single_activity_attachment';

    public function buildQuickForm() {

        $this->contact_id = CRM_Utils_Request::retrieve('cid', 'Int', $this, true);

        $this->assign('user_id', $this->contact_id);

        $this->setTitle(E::ts("CiviOffice - Generate a single Document"));

        $config = CRM_Civioffice_Configuration::getConfig();

        // add list of document renderers and supported output mime types
        $output_mimetypes = null;
        $document_renderer_list = [];
        foreach ($config->getDocumentRenderers(true) as $dr) {
            foreach ($dr->getSupportedOutputMimeTypes() as $mime_type) {
                $output_mimetypes[$mime_type] = CRM_Civioffice_MimeType::mapMimeTypeToFileExtension($mime_type);
            }
            $document_renderer_list[$dr->getURI()] = $dr->getName();
        }
        $this->add(
            'select',
            'document_renderer_uri',
            E::ts("Document Renderer"),
            $document_renderer_list,
            true,
            ['class' => 'crm-select2 huge']
        );

        // build document list
        $document_list = [];
        // todo: only show supported source mime types
        foreach ($config->getDocumentStores(true) as $document_store) {
            foreach ($document_store->getDocuments() as $document) {  // todo: recursive
                /** @var CRM_Civioffice_Document $document */

                // TODO: Mimetype checks could be handled differently in the future: https://github.com/systopia/de.systopia.civioffice/issues/2
                if (!CRM_Civioffice_MimeType::hasSpecificFileNameExtension($document->getName(), CRM_Civioffice_MimeType::DOCX)) {
                    continue; // for now only allow/return docx files
                }

                $document_list[$document->getURI()] = "[{$document_store->getName()}] {$document->getName()}";
            }
        }
        $this->add(
            'select',
            'document_uri',
            E::ts("Document"),
            $document_list,
            true,
            ['class' => 'crm-select2 huge']
        );

        $this->add(
            'select',
            'target_mime_type',
            E::ts("Target document type"),
            $output_mimetypes,
            true,
            ['class' => 'crm-select2']
        );

        $this->add(
            'select',
            'activity_type_id',
            E::ts("Create Activity"),
            $this->getActivityTypes(),
            false,
            ['class' => 'crm-select2']
        );

        $this->add(
            'checkbox',
            'activity_attach_doc',
            E::ts("Attach Rendered Document")
        );

        // set last values
        try {
            $this->setDefaults([
               'activity_type_id' => Civi::contactSettings()->get(self::UNOCONV_CREATE_SINGLE_ACTIVIY_TYPE),
               'activity_attach_doc' => Civi::contactSettings()->get(self::UNOCONV_CREATE_SINGLE_ACTIVIY_ATTACHMENT),
           ]);
        } catch (CRM_Core_Exception $ex) {
            Civi::log()->warning("CiviOffice: Couldn't restore defaults: " . $ex->getMessage());
        }

        // add buttons
        $this->addButtons([
              [
                  'type' => 'preview',
                  'name' => E::ts("Preview"),
                  'icon' => 'fa-search',
                  'isDefault' => FALSE,
              ],
              [
                  'type' => 'close',
                  'name' => E::ts("Cancel"),
                  'icon' => 'fa-window-close-o',
                  'isDefault' => FALSE,
              ],
              [
                  'type' => 'submit',
                  'name' => E::ts("Download Document"),
                  'icon' => 'fa-file-pdf-o',
                  'isDefault' => FALSE,
              ],
        ]);

        // add script to handle the special buttons
        Civi::resources()->addScriptUrl(E::url('js/create_single_document.js'));
        Civi::resources()->addVars('civioffice_single_document', [
            'contact_id'    => $this->contact_id,
            'renderer_url'  => CRM_Utils_System::url('civicrm/civioffice/render'),
            'document_name' => E::ts('document.'),
            'mime2suffix'   => CRM_Civioffice_MimeType::mimeTypeToFileExtensionMap(),
        ]);
    }


    public function postProcess() {
        $values = $this->exportValues();

        // save defaults
        try {
            Civi::contactSettings()->set(self::UNOCONV_CREATE_SINGLE_ACTIVIY_TYPE, $values['activity_type_id'] ?? '');
            Civi::contactSettings()->set(self::UNOCONV_CREATE_SINGLE_ACTIVIY_ATTACHMENT, $values['activity_attach_doc'] ?? 0);
        } catch (CRM_Core_Exception $ex) {
            Civi::log()->warning("CiviOffice: Couldn't save defaults: " . $ex->getMessage());
        }

        // render document
        $render_result = civicrm_api3('CiviOffice', 'convert', [
            'document_uri'     => $values['document_uri'],
            'entity_ids'       => [$this->contact_id],
            'entity_type'      => 'contact',
            'renderer_uri'     => $values['document_renderer_uri'],
            'target_mime_type' => $values['target_mime_type']
        ]);
        $result_store_uri = $render_result['values'][0];
        $store = CRM_Civioffice_Configuration::getDocumentStore($result_store_uri);
        $rendered_documents = $store->getDocuments();
        /** @var CRM_Civioffice_Document $rendered_document */
        $rendered_document = reset($rendered_documents);

        // if user wants an activity, create + add the document
        if (!empty($values['activity_type_id'])) {
            $activity = civicrm_api3('Activity', 'create', [
                'activity_type_id'   => $values['activity_type_id'],
                'subject'            => E::ts("Document (CiviOffice)"),
                'status_id'          => 'Completed',
                'activity_date_time' => date("YmdHis"),
                'target_id'          => [$this->contact_id],
            ]);

            // generate & link attachment if requested
            if (!empty($values['activity_attach_doc'])) {
                $path_of_local_copy = $rendered_document->getLocalTempCopy();
                // attach rendered document
                $attachments = [
                    'attachFile_1' => [
                        'location' => $path_of_local_copy,
                        'type' => mime_content_type($path_of_local_copy)
                    ]
                ];
                CRM_Core_BAO_File::processAttachment($attachments, 'civicrm_activity', $activity['id']);
            }
        }

        // finally: download the document (render it again)
        $render_url = CRM_Utils_System::url('civicrm/civioffice/render', 'contact_ids=2');
        $render_url .= "&document_uri=" . base64_encode($values['document_uri']);
        $render_url .= "&renderer_uri=" . base64_encode($values['document_renderer_uri']);
        $render_url .= "&target_mime_type=" . base64_encode($values['target_mime_type']);
        CRM_Utils_System::redirect($render_url);
    }




    /**
     * Get a list of eligible activity types
     */
    protected function getActivityTypes()
    {
        $types = ['' => E::ts("- none -")];
        $type_query = civicrm_api3('OptionValue', 'get', [
            'option_group_id' => 'activity_type',
            'is_reserved' => 0,
            'option.limit' => 0,
            'return' => 'value,label'
        ]);
        foreach ($type_query['values'] as $type) {
            $types[$type['value']] = $type['label'];
        }
        return $types;
    }
}
