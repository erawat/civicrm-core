<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * Class CRM_Utils_Mail_CaseMail.
 */
class CRM_Utils_Mail_CaseMail {

  /**
   * Checks if email is related to cases.
   *
   * @param string $subject
   *   Email subject.
   *
   * @return bool
   *   TRUE if email subject contains case ID or case token, FALSE otherwise.
   */
  public function isCaseEmail ($subject) {
    $subject = trim($subject);
    $res = preg_match('/\[case #([0-9a-h]{7})\]/', $subject) === 1
      || preg_match('/\[case #(\d+)\]/', $subject) === 1;

    return $res;
  }

}
