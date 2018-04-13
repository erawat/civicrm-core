'use strict';

describe('crmCaseType', function() {
  var $controller;
  var apiCalls;
  var ctrl;
  var compile;
  var $httpBackend;
  var scope;
  var timeout;

  beforeEach(function() {
    CRM.resourceUrls = {
      'civicrm': ''
    };
    // CRM_Case_XMLProcessor::REL_TYPE_CNAME
    CRM.crmCaseType = {
      'REL_TYPE_CNAME': 'label_b_a'
    };
    module('crmCaseType');
    module('crmJsonComparator');
    inject(function(crmJsonComparator) {
      crmJsonComparator.register(jasmine);
    });
  });

  beforeEach(inject(function(_$controller_, _$httpBackend_, $compile, $rootScope, $timeout) {
    $httpBackend = _$httpBackend_;
    $controller = _$controller_;
    scope = $rootScope.$new();
    compile = $compile;
    timeout = $timeout;
  }));

  describe('CaseTypeCtrl', function() {
    beforeEach(function () {
      apiCalls = {
        caseTypeCategories: getCaseTypeCategoriesSampleData(),
        actStatuses: {
          values: [
            {
              "id": "272",
              "option_group_id": "25",
              "label": "Scheduled",
              "value": "1",
              "name": "Scheduled",
              "filter": "0",
              "is_default": "1",
              "weight": "1",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1"
            },
            {
              "id": "273",
              "option_group_id": "25",
              "label": "Completed",
              "value": "2",
              "name": "Completed",
              "filter": "0",
              "weight": "2",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1"
            }
          ]
        },
        caseStatuses: {
          values: [
            {
              "id": "290",
              "option_group_id": "28",
              "label": "Ongoing",
              "value": "1",
              "name": "Open",
              "grouping": "Opened",
              "filter": "0",
              "is_default": "1",
              "weight": "1",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1"
            },
            {
              "id": "291",
              "option_group_id": "28",
              "label": "Resolved",
              "value": "2",
              "name": "Closed",
              "grouping": "Closed",
              "filter": "0",
              "weight": "2",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1"
            }
          ]
        },
        actTypes: {
          values: [
            {
              "id": "784",
              "option_group_id": "2",
              "label": "ADC referral",
              "value": "62",
              "name": "ADC referral",
              "filter": "0",
              "is_default": "0",
              "weight": "64",
              "is_optgroup": "0",
              "is_reserved": "0",
              "is_active": "1",
              "component_id": "7"
            },
            {
              "id": "32",
              "option_group_id": "2",
              "label": "Add Client To Case",
              "value": "27",
              "name": "Add Client To Case",
              "filter": "0",
              "is_default": "0",
              "weight": "26",
              "description": "",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1",
              "component_id": "7"
            },
            {
              "id": "18",
              "option_group_id": "2",
              "label": "Open Case",
              "value": "13",
              "name": "Open Case",
              "filter": "0",
              "is_default": "0",
              "weight": "13",
              "is_optgroup": "0",
              "is_reserved": "1",
              "is_active": "1",
              "component_id": "7",
              "icon": "fa-folder-open-o"
            },
            {
              "id": "857",
              "option_group_id": "2",
              "label": "Medical evaluation",
              "value": "55",
              "name": "Medical evaluation",
              "filter": "0",
              "is_default": "0",
              "weight": "56",
              "is_optgroup": "0",
              "is_reserved": "0",
              "is_active": "1",
              "component_id": "7"
            },
          ]
        },
        relTypes: {
          values: [
            {
              "id": "14",
              "name_a_b": "Benefits Specialist is",
              "label_a_b": "Benefits Specialist is",
              "name_b_a": "Benefits Specialist",
              "label_b_a": "Benefits Specialist",
              "description": "Benefits Specialist",
              "contact_type_a": "Individual",
              "contact_type_b": "Individual",
              "is_reserved": "0",
              "is_active": "1"
            },
            {
              "id": "9",
              "name_a_b": "Case Coordinator is",
              "label_a_b": "Case Coordinator is",
              "name_b_a": "Case Coordinator",
              "label_b_a": "Case Coordinator",
              "description": "Case Coordinator",
              "contact_type_a": "Individual",
              "contact_type_b": "Individual",
              "is_reserved": "0",
              "is_active": "1"
            }
          ]
        },
        caseType: {
          "id": "1",
          "name": "housing_support",
          "title": "Housing Support",
          "description": "Help homeless individuals obtain temporary and long-term housing",
          "is_active": "1",
          "is_reserved": "0",
          "weight": "1",
          "is_forkable": "1",
          "is_forked": "",
          "definition": {
            "activityTypes": [
              {"name": "Open Case", "max_instances": "1"}
            ],
            "activitySets": [
              {
                "name": "standard_timeline",
                "label": "Standard Timeline",
                "timeline": "1",
                "activityTypes": [
                  {
                    "name": "Open Case",
                    "status": "Completed"
                  },
                  {
                    "name": "Medical evaluation",
                    "reference_activity": "Open Case",
                    "reference_offset": "1",
                    "reference_select": "newest"
                  }
                ]
              }
            ],
            "caseRoles": [
              {
                "name": "Homeless Services Coordinator",
                "creator": "1",
                "manager": "1"
              }
            ]
          }
        },
        defaultAssigneeTypes: {
          values: [
              {
                "id": "1174",
                "option_group_id": "152",
                "label": "None",
                "value": "1",
                "name": "NONE",
                "filter": "0",
                "is_default": "0",
                "weight": "1",
                "is_optgroup": "0",
                "is_reserved": "0",
                "is_active": "1"
              },
              {
                "id": "1175",
                "option_group_id": "152",
                "label": "By relationship to workflow client",
                "value": "2",
                "name": "BY_RELATIONSHIP",
                "filter": "0",
                "is_default": "0",
                "weight": "2",
                "is_optgroup": "0",
                "is_reserved": "0",
                "is_active": "1"
              },
              {
                "id": "1176",
                "option_group_id": "152",
                "label": "Specific contact",
                "value": "3",
                "name": "SPECIFIC_CONTACT",
                "filter": "0",
                "is_default": "0",
                "weight": "3",
                "is_optgroup": "0",
                "is_reserved": "0",
                "is_active": "1"
              },
              {
                "id": "1177",
                "option_group_id": "152",
                "label": "User creating the workflow",
                "value": "4",
                "name": "USER_CREATING_THE_CASE",
                "filter": "0",
                "is_default": "0",
                "weight": "4",
                "is_optgroup": "0",
                "is_reserved": "0",
                "is_active": "1"
              }
          ]
        }
      };
      ctrl = $controller('CaseTypeCtrl', {$scope: scope, apiCalls: apiCalls});
    });

    it('should load activity statuses', function() {
      expect(scope.activityStatuses).toEqualData(apiCalls.actStatuses.values);
    });

    it('should load activity types', function() {
      expect(scope.activityTypes['ADC referral']).toEqualData(apiCalls.actTypes.values[0]);
    });

    it('should load case type categories', function() {
      expect(scope.caseTypeCategories).toEqual(apiCalls.caseTypeCategories.values);
    });

    it('should store the default assignee types', function() {
      expect(scope.defaultAssigneeTypes).toBe(apiCalls.defaultAssigneeTypes.values);
    });

    it('should store the default assignee types indexed by name', function() {
      expect(scope.defaultAssigneeTypesIndex).toEqual(_.indexBy(apiCalls.defaultAssigneeTypes.values, 'name'));
    });

    it('addActivitySet should add an activitySet to the case type', function() {
      scope.addActivitySet('timeline');
      var activitySets = scope.caseType.definition.activitySets;
      var newSet = activitySets[activitySets.length - 1];
      expect(newSet.name).toBe('timeline_1');
      expect(newSet.timeline).toBe('1');
      expect(newSet.label).toBe('Timeline');
    });

    it('addActivitySet handles second timeline correctly', function() {
      scope.addActivitySet('timeline');
      scope.addActivitySet('timeline');
      var activitySets = scope.caseType.definition.activitySets;
      var newSet = activitySets[activitySets.length - 1];
      expect(newSet.name).toBe('timeline_2');
      expect(newSet.timeline).toBe('1');
      expect(newSet.label).toBe('Timeline #2');
    });

    describe('when clearing the activity\'s default assignee type values', function() {
      var activity;

      beforeEach(function() {
        activity = {
          default_assignee_relationship: 1,
          default_assignee_contact: 2
        };

        scope.clearActivityDefaultAssigneeValues(activity);
      });

      it('clears the default assignee relationship for the activity', function() {
        expect(activity.default_assignee_relationship).toBe(null);
      });

      it('clears the default assignee contact for the activity', function() {
        expect(activity.default_assignee_contact).toBe(null);
      });
    });

    describe('when adding a new activity to a set', function() {
      var activitySet;

      beforeEach(function() {
        activitySet = { activityTypes: [] };
        scope.activityTypes = { comment: { label: 'Add a new comment' } };

        scope.addActivity(activitySet, 'comment');
      });

      it('adds a new Comment activity to the set', function() {
        expect(activitySet.activityTypes[0]).toEqual({
          name: 'comment',
          label: scope.activityTypes.comment.label,
          status: 'Scheduled',
          reference_activity: 'Open Case',
          reference_offset: '1',
          reference_select: 'newest',
          default_assignee_type: scope.defaultAssigneeTypesIndex.NONE.id
        });
      });
    });

    describe('when creating a new case type', function() {
      var defaultCategory;

      beforeEach(inject(function ($controller) {
        apiCalls.caseType = null;

        defaultCategory = _.find(apiCalls.caseTypeCategories.values, { name: 'WORKFLOW' }) || {};

        ctrl = $controller('CaseTypeCtrl', {$scope: scope, apiCalls: apiCalls});
      }));

      it('sets default values for the case type title, name, and active status', function() {
        expect(scope.caseType).toEqual(jasmine.objectContaining({
          title: '',
          name: '',
          is_active: '1'
        }));
      });

      it('sets workflow as the default category', function() {
        expect(scope.caseType.category).toEqual(defaultCategory.value);
      });

      it('adds an Open Case activty to the default activty set', function() {
        expect(scope.caseType.definition.activitySets[0].activityTypes).toEqual([{
          name: 'Open Case',
          label: 'Open Case',
          status: 'Completed',
          default_assignee_type: scope.defaultAssigneeTypesIndex.NONE.id
        }]);
      });
    });
  });

  describe('CaseTypeListCtrl', function() {
    var caseTypeCategoriesIndex;
    var caseTypes = {
      values: [{ id: _.uniqueId() }]
    };

    beforeEach(function() {
      var caseTypeCategories = getCaseTypeCategoriesSampleData();
      caseTypeCategoriesIndex = _.indexBy(caseTypeCategories.values, 'value');
      ctrl = $controller('CaseTypeListCtrl', {
        $scope: scope,
        caseTypes: caseTypes,
        caseTypeCategories: caseTypeCategories
      });
    });

    it('should store a list of case types', function() {
      expect(scope.caseTypes).toEqual(caseTypes.values);
    });

    it('should store a list of case type categories indexed by value', function() {
      expect(scope.caseTypeCategoriesIndex).toEqual(caseTypeCategoriesIndex);
    });
  });

  /**
   * Returns a sample api response for case type categories option values.
   */
  function getCaseTypeCategoriesSampleData() {
    return {
      values: [
        {
          "id": "1170",
          "option_group_id": "153",
          "label": "Workflow",
          "value": "1",
          "name": "WORKFLOW",
          "filter": "0",
          "is_default": "0",
          "weight": "1",
          "is_optgroup": "0",
          "is_reserved": "1",
          "is_active": "1"
        },
        {
          "id": "1171",
          "option_group_id": "153",
          "label": "Vacancy",
          "value": "2",
          "name": "VACANCY",
          "filter": "0",
          "is_default": "0",
          "weight": "2",
          "is_optgroup": "0",
          "is_reserved": "1",
          "is_active": "1"
        }
      ]
    };
  }
});
