<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 * Upgrade logic for 5.0
 */
class CRM_Upgrade_Incremental_php_FiveZero extends CRM_Upgrade_Incremental_Base {

  /**
   * Compute any messages which should be displayed beforeupgrade.
   *
   * Note: This function is called iteratively for each upcoming
   * revision to the database.
   *
   * @param string $preUpgradeMessage
   * @param string $rev
   *   a version number, e.g. '4.4.alpha1', '4.4.beta3', '4.4.0'.
   * @param null $currentVer
   */
  public function setPreUpgradeMessage(&$preUpgradeMessage, $rev, $currentVer = NULL) {
    // Example: Generate a pre-upgrade message.
    //if ($rev == '5.12.34') {
    //  $preUpgradeMessage .= '<p>' . ts('A new permission has been added called %1 This Permission is now used to control access to the Manage Tags screen', array(1 => 'manage tags')) . '</p>';
    //}
  }

  /**
   * Compute any messages which should be displayed after upgrade.
   *
   * @param string $postUpgradeMessage
   *   alterable.
   * @param string $rev
   *   an intermediate version; note that setPostUpgradeMessage is called repeatedly with different $revs.
   */
  public function setPostUpgradeMessage(&$postUpgradeMessage, $rev) {
    // Example: Generate a post-upgrade message.
    //if ($rev == '5.12.34') {
    //  $postUpgradeMessage .= '<br /><br />' . ts("By default, CiviCRM now disables the ability to import directly from SQL. To use this feature, you must explicitly grant permission 'import SQL datasource'.");
    //}
  }

  /**
   * Upgrade function.
   *
   * @param string $rev
   */
  public function upgrade_5_0_0($rev) {
    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => $rev)), 'runSql', $rev);
  }

  /**
   * Upgrade function for version 5.0.1. This version adds the default assignee
   * option values that can be selected when creating or editing a new
   * workflow's activity.
   *
   * @param string $rev
   */
  public function upgrade_5_0_1($rev) {
    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => $rev)), 'runSql', $rev);

    // Add option group for activity default assignees:
    CRM_Core_BAO_OptionGroup::ensureOptionGroupExists([
      'name' => 'activity_default_assignee',
      'title' => ts('Activity default assignee'),
      'is_reserved' => 1,
    ]);

    CRM_Core_PseudoConstant::flush();

    // Add option values for activity default assignees:
    $options = [
      ['name' => 'NONE', 'label' => ts('None')],
      ['name' => 'BY_RELATIONSHIP', 'label' => ts('By relationship to case client')],
      ['name' => 'SPECIFIC_CONTACT', 'label' => ts('Specific contact')],
      ['name' => 'USER_CREATING_THE_CASE', 'label' => ts('User creating the case')]
    ];

    foreach ($options as $option) {
      CRM_Core_BAO_OptionValue::ensureOptionValueExists([
        'option_group_id' => 'activity_default_assignee',
        'name' => $option['name'],
        'label' => $option['label'],
        'is_active' => TRUE
      ]);
    }
  }

  /**
   * Upgrade function for version 5.0.2. This version adds the category column
   * to case types. The category is an option value that can be Workflow or
   * Vacancy.
   *
   * @param string $rev
   */
  public function upgrade_5_0_2($rev) {
    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => $rev)), 'runSql', $rev);

    $this->_addCategoryColumnToCaseType();
    $this->_createCaseTypeCategories();
    $this->_setDefaultCategoriesForCaseTypes();
  }

  /**
   * Adds a new category column to the case type entity. This column references
   * option values.
   */
  protected function _addCategoryColumnToCaseType() {
    CRM_Core_DAO::executeQuery('ALTER TABLE civicrm_case_type
      ADD COLUMN category VARCHAR(512)');
  }

  /**
   * Creates the option group and values for the case type categories. The
   * values can be Vacancy or Workflow types.
   */
  protected function _createCaseTypeCategories() {
    CRM_Core_BAO_OptionGroup::ensureOptionGroupExists([
      'name' => 'case_type_category',
      'title' => ts('Case Type Category'),
      'is_reserved' => 1,
    ]);

    CRM_Core_PseudoConstant::flush();

    $options = [
      ['name' => 'WORKFLOW', 'label' => ts('Workflow')],
      ['name' => 'VACANCY', 'label' => ts('Vacancy')]
    ];

    foreach ($options as $option) {
      CRM_Core_BAO_OptionValue::ensureOptionValueExists([
        'option_group_id' => 'case_type_category',
        'name' => $option['name'],
        'label' => $option['label'],
        'is_active' => TRUE,
        'is_reserved'=> TRUE
      ]);
    }
  }

  /**
   * Updates current case types so they have a category assigned. All case types
   * are assigned the Workflow category by default except for the Application case
   * type, which gets the Vacancy category.
   */
  protected function _setDefaultCategoriesForCaseTypes() {
    $caseTypes = civicrm_api3('CaseType', 'get', [
      'options' => [ 'limit' => 0 ]
    ]);

    foreach ($caseTypes['values'] as $caseType) {
      $category = $caseType['name'] === 'Application' ? 'VACANCY' : 'WORKFLOW';

      civicrm_api3('CaseType', 'create', [
        'id' => $caseType['id'],
        'category' => $category
      ]);
    }
  }

  /*
   * Important! All upgrade functions MUST add a 'runSql' task.
   * Uncomment and use the following template for a new upgrade version
   * (change the x in the function name):
   */

  //  /**
  //   * Upgrade function.
  //   *
  //   * @param string $rev
  //   */
  //  public function upgrade_4_7_x($rev) {
  //    $this->addTask(ts('Upgrade DB to %1: SQL', array(1 => $rev)), 'runSql', $rev);
  //    $this->addTask(ts('Do the foo change'), 'taskFoo', ...);
  //    // Additional tasks here...
  //    // Note: do not use ts() in the addTask description because it adds unnecessary strings to transifex.
  //    // The above is an exception because 'Upgrade DB to %1: SQL' is generic & reusable.
  //  }

  // public static function taskFoo(CRM_Queue_TaskContext $ctx, ...) {
  //   return TRUE;
  // }

}
