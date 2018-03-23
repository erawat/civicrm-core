<?php
require_once 'CiviTest/CiviCaseTestCase.php';

/**
 * Class CRM_Case_PseudoConstantTest
 * @group headless
 */
class CRM_Case_XMLProcessor_ProcessTest extends CiviCaseTestCase {

  public function setUp() {
    parent::setUp();

    $this->defaultAssigneeOptionsIds = array();
    $this->assigneeContactId = $this->individualCreate();
    $this->targetContactId = $this->individualCreate();

    $this->setUpDefaultAssigneeOptions();
    $this->setUpRelationship();

    $this->activityTypeXml = (object) array(
      'name' => 'Open Case'
    );
    $this->params = array(
      'activity_date_time' => date('Ymd'),
      'caseID' => $this->caseTypeId,
      'clientID' => $this->targetContactId,
      'creatorID' => $this->assigneeContactId,
    );

    $this->process = new CRM_Case_XMLProcessor_Process();
  }

  /**
   * Adds the default assignee group and options to the test database.
   * It also stores the IDs of the options in an index.
   */
  protected function setUpDefaultAssigneeOptions() {
    $options = array(
      'NONE', 'BY_RELATIONSHIP', 'SPECIFIC_CONTACT', 'USER_CREATING_THE_CASE'
    );

    CRM_Core_BAO_OptionGroup::ensureOptionGroupExists(array(
      'name' => 'activity_default_assignee'
    ));

    foreach ($options as $option) {
      $optionValueId = CRM_Core_BAO_OptionValue::ensureOptionValueExists(array(
        'option_group_id' => 'activity_default_assignee',
        'name' => $option,
        'label' => $option
      ));

      $this->defaultAssigneeOptionsIds[$option] = $optionValueId;
    }
  }

  /**
   * Adds a relationship between the activity's target contact and default assignee.
   */
  protected function setUpRelationship() {
    $this->relationshipTypeId = $this->relationshipTypeCreate(array(
      'contact_type_a' => 'Individual',
      'contact_type_b' => 'Individual'
    ));
    $this->relationshipId = $this->callAPISuccess('Relationship', 'create', array(
      'contact_id_a' => $this->assigneeContactId,
      'contact_id_b' => $this->targetContactId,
      'relationship_type_id' => $this->relationshipTypeId
    ));
  }

  /**
   * Tests the creation of activities with default assignee by relationship.
   */
  public function testCreateActivityWithDefaultContactByRelationship() {
    $this->activityTypeXml->default_assignee_type = $this->defaultAssigneeOptionsIds['BY_RELATIONSHIP'];
    $this->activityTypeXml->default_assignee_relationship = $this->relationshipTypeId;

    $this->process->createActivity($this->activityTypeXml, $this->params);
    $this->assertActivityAssignedToContactExists();
  }

  /**
   * Asserts that a an activity was created where the assignee was the one related
   * to the target contact.
   * It also deletes this activity from the test database.
   */
  protected function assertActivityAssignedToContactExists() {
    $activity = $this->callAPISuccess('Activity', 'getsingle', array(
      'assignee_contact_id' => $this->assigneeContactId
    ));

    $this->assertNotNull($activity, 'Contact has no activities assigned to them');

    CRM_Activity_BAO_Activity::deleteActivityContact($activity['id'], 1);
  }

}
