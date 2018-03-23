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
    $this->unassignedRelationshipTypeId = $this->relationshipTypeCreate(array(
      'name_a_b' => 'Instructor of',
      'name_b_a' => 'Pupil of'
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
    $this->assertActivityAssignedToContactExists($this->assigneeContactId);
  }

  /**
   * Tests the creation of activities with default assignee by relationship,
   * but the target contact doesn't have any relationship of the selected type.
   */
  public function testCreateActivityWithDefaultContactByRelationButTheresNoRelationship() {
    $this->activityTypeXml->default_assignee_type = $this->defaultAssigneeOptionsIds['BY_RELATIONSHIP'];
    $this->activityTypeXml->default_assignee_relationship = $this->unassignedRelationshipTypeId;

    $this->process->createActivity($this->activityTypeXml, $this->params);
    $this->assertActivityAssignedToContactExists(NULL);
  }

  /**
   * Tests the creation of activities with default assignee set to a specific contact.
   */
  public function testCreateActivityAssignedToSpecificContact() {
    $this->activityTypeXml->default_assignee_type = $this->defaultAssigneeOptionsIds['SPECIFIC_CONTACT'];
    $this->activityTypeXml->default_assignee_contact = $this->assigneeContactId;

    $this->process->createActivity($this->activityTypeXml, $this->params);
    $this->assertActivityAssignedToContactExists($this->assigneeContactId);
  }

  /**
   * Tests the creation of activities with default assignee set to a specific contact,
   * but the contact does not exist.
   */
  public function testCreateActivityAssignedToNonExistantSpecificContact() {
    $this->activityTypeXml->default_assignee_type = $this->defaultAssigneeOptionsIds['SPECIFIC_CONTACT'];
    $this->activityTypeXml->default_assignee_contact = 987456321;

    $this->process->createActivity($this->activityTypeXml, $this->params);
    $this->assertActivityAssignedToContactExists(NULL);
  }

  /**
   * Asserts that a an activity was created where the assignee was the one related
   * to the target contact.
   * It also deletes this activity from the test database.
   *
   * @param int|null the ID of the expected assigned contact or NULL if expected to be empty.
   */
  protected function assertActivityAssignedToContactExists($assigneeContactId) {
    $activity = $this->callAPISuccess('Activity', 'getsingle', array(
      'target_contact_id' => $this->targetContactId,
      'assignee_contact_id' => $assigneeContactId
    ));

    $this->assertNotNull($activity, 'Contact has no activities assigned to them');

    CRM_Activity_BAO_Activity::deleteActivityContact($activity['id'], 1);
  }

}
