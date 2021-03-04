<?php

use CRM_Civioffice_ExtensionUtil as E;

class CRM_Civioffice_GenerateConversionJob
{
    /**
     * @var string $renderer_id
     *  ID which specifies the selected renderer being used
     */
    protected $renderer_id;

    /**
     * @var $document_id
     *   The template id which is used for generating a document
     */
    protected $document_id;

    /**
     * @var array $entity_IDs
     *   Array with entity IDs (like contact IDs)
     */
    protected $entity_IDs;

    /**
     * @var string $target_mime_type
     *   Mime type of target file (like pdf)
     */
    protected $target_mime_type;

    /**
     * @var string $entity_type
     *   Type of entity (like 'contact' ID)
     */
    protected $entity_type;

    /**
     * @var string $title
     *   Title for runner state
     */
    public $title;

    public function __construct($renderer_id, $document_id, $entity_IDs, $target_mime_type, $entity_type, $title)
    {
        $this->renderer_id = $renderer_id;
        $this->document_id = $document_id;
        $this->entity_IDs = $entity_IDs;
        $this->target_mime_type = $target_mime_type;
        $this->entity_type = $entity_type;
        $this->title = $title;
    }

    public function run(): bool
    {

        $configuration = new CRM_Civioffice_Configuration();
        $config = $configuration::getConfig();
        $document_renderer = $configuration->getDocumentRenderer($this->renderer_id);

        $document = $config->getDocument($this->document_id);

        $document_renderer->render($document, $this->entity_IDs, $this->target_mime_type, $this->entity_type);

        return true;
    }
}