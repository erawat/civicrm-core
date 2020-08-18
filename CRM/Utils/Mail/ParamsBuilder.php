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
 * Class CRM_Utils_Mail_ParamsBuilder.
 */
class CRM_Utils_Mail_ParamsBuilder {

  /**
   * @param $result
   * @param int $activityTypeID
   *
   * @return array
   *   <type> $params
   */
  public function buildActivityParams($result, $activityTypeID) {
    // get ready for collecting data about activity to be created
    $params = [];

    $params['activity_type_id'] = $activityTypeID;

    $params['status_id'] = 'Completed';
    if (!empty($result['from']['id'])) {
      $params['source_contact_id'] = $params['assignee_contact_id'] = $result['from']['id'];
    }
    $params['target_contact_id'] = [];
    $keys = ['to', 'cc', 'bcc'];
    foreach ($keys as $key) {
      if (is_array($result[$key])) {
        foreach ($result[$key] as $key => $keyValue) {
          if (!empty($keyValue['id'])) {
            $params['target_contact_id'][] = $keyValue['id'];
          }
        }
      }
    }
    $params['subject'] = $result['subject'];
    $params['activity_date_time'] = $result['date'];
    $params['details'] = $result['body'];

    $numAttachments = Civi::settings()->get('max_attachments_backend') ?? CRM_Core_BAO_File::DEFAULT_MAX_ATTACHMENTS_BACKEND;
    for ($i = 1; $i <= $numAttachments; $i++) {
      if (isset($result["attachFile_$i"])) {
        $params["attachFile_$i"] = $result["attachFile_$i"];
      }
      else {
        // No point looping 100 times if there's only one attachment
        break;
      }
    }

    return $params;
  }

}
