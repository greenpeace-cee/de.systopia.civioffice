<?php
/*-------------------------------------------------------+
| SYSTOPIA CiviOffice Integration                        |
| Copyright (C) 2022 SYSTOPIA                            |
| Author: J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*/

use Civi\Token\TokenProcessor;
use CRM_Civioffice_ExtensionUtil as E;

class CRM_Civioffice_Page_Tokens extends CRM_Core_Page
{
    public function run()
    {
        $tokenProcessor = new TokenProcessor(
            Civi::service('dispatcher'),
            [
                'schema' => [
                    'contactId',
                    'contributionId',
                    'participantId',
                    'eventId',
                    'caseId',
                ],
                'controller' => __CLASS__,
                'smarty' => false,
            ]
        );
        $this->assign('tokens', CRM_Utils_Token::formatTokensForDisplay($tokenProcessor->listTokens()));

        return parent::run();
    }
}
