<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method modify\\(\\) on DateTimeImmutable\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Analytics\\\\AnalyticsService\\:\\:build\\(\\) has parameter \\$settings with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Analytics\\\\AnalyticsService\\:\\:firstFromFinder\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Analytics\\\\AnalyticsService\\:\\:retrieveConceptNamesExport\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Analytics\\\\AnalyticsService\\:\\:retrieveTrackingDataExport\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Entity\\\\PageLoad\\:\\:setStudyArea\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$timestamp of method App\\\\Entity\\\\PageLoad\\:\\:setTimestamp\\(\\) expects DateTime, DateTime\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$content of method Symfony\\\\Component\\\\Filesystem\\\\Filesystem\\:\\:dumpFile\\(\\) expects resource\\|string, string\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method format\\(\\) on DateTimeImmutable\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Analytics\\\\Model\\\\SynthesizeRequest\\:\\:getSettings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Analytics\\\\Model\\\\SynthesizeRequest\\:\\:validate\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	// identifier: classConstant.nonObject
	'message' => '#^Cannot access constant class on Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:createDataResponse\\(\\) has parameter \\$extraData with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:createDataResponse\\(\\) has parameter \\$serializationGroups with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:getArrayFromBody\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(object\\)\\: mixed\\)\\|null, Closure\\(App\\\\Entity\\\\Concept\\)\\: App\\\\Entity\\\\Concept given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objects of class Drenso\\\\Shared\\\\IdMap\\\\IdMap constructor expects array\\<Drenso\\\\Shared\\\\Interfaces\\\\IdInterface\\>, array\\<object\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$tag of method App\\\\Entity\\\\Concept\\:\\:addTag\\(\\) expects App\\\\Entity\\\\Tag, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: varTag.differentVariable
	'message' => '#^Variable \\$requestTag in PHPDoc tag @var does not match assigned variable \\$requestTags\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objects of class Drenso\\\\Shared\\\\IdMap\\\\IdMap constructor expects array\\<Drenso\\\\Shared\\\\Interfaces\\\\IdInterface\\>, array\\<object\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$relationType of method App\\\\Entity\\\\ConceptRelation\\:\\:setRelationType\\(\\) expects App\\\\Entity\\\\RelationType\\|null, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$source of method App\\\\Entity\\\\ConceptRelation\\:\\:setSource\\(\\) expects App\\\\Entity\\\\Concept, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$target of method App\\\\Entity\\\\ConceptRelation\\:\\:setTarget\\(\\) expects App\\\\Entity\\\\Concept, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$object of method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:assertStudyAreaObject\\(\\) expects App\\\\Entity\\\\Contracts\\\\StudyAreaFilteredInterface, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$relationType of method App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:mapToEntity\\(\\) expects App\\\\Entity\\\\RelationType\\|null, object\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Api/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Api/Controller/StylingConfigurationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$content of attribute class OpenApi\\\\Attributes\\\\RequestBody constructor expects array\\<OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\>\\|OpenApi\\\\Attributes\\\\Attachable\\|OpenApi\\\\Attributes\\\\JsonContent\\|OpenApi\\\\Attributes\\\\MediaType\\|OpenApi\\\\Attributes\\\\XmlContent\\|null, array\\<int, Nelmio\\\\ApiDocBundle\\\\Annotation\\\\Model\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Api/Controller/TagController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$outgoingRelations with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\ConceptApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\RelationTypeApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/RelationTypeApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\StudyAreaApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\StudyAreaApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getStylings\\(\\) on App\\\\Entity\\\\StylingConfiguration\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$stylings with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\StylingConfigurationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\TagApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/TagApiModel.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDotronConfig\\(\\) on App\\\\Entity\\\\ConceptRelation\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getRelationType\\(\\) on App\\\\Entity\\\\ConceptRelation\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: assign.readOnlyProperty
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$id is already assigned\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: assign.readOnlyProperty
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$sourceId is already assigned\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: assign.readOnlyProperty
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$targetId is already assigned\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	// identifier: property.uninitializedReadonly
	'message' => '#^Class App\\\\Api\\\\Model\\\\Validation\\\\ValidationError has an uninitialized readonly property \\$message\\. Assign it in the constructor\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationError.php',
];
$ignoreErrors[] = [
	// identifier: property.uninitializedReadonly
	'message' => '#^Class App\\\\Api\\\\Model\\\\Validation\\\\ValidationError has an uninitialized readonly property \\$propertyPath\\. Assign it in the constructor\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationError.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Api\\\\Model\\\\Validation\\\\ValidationFailedData\\:\\:__construct\\(\\) has parameter \\$violations with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationFailedData.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Anonymous function should return App\\\\Entity\\\\UserApiToken\\|null but returns object\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Security/ApiAuthenticator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Api\\\\Security\\\\ApiAuthenticator\\:\\:createToken\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Security/ApiAuthenticator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Api\\\\Security\\\\ApiAuthenticator\\:\\:onAuthenticationSuccess\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Security/ApiAuthenticator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Attribute\\\\DenyOnFrozenStudyArea\\:\\:__construct\\(\\) has parameter \\$routeParams with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Attribute/DenyOnFrozenStudyArea.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getAddress\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 11,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:reviewRequested\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionApproved\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionDenied\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionPublished\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:trans\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Communication\\\\SetFromSubscriber\\:\\:onMessage\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/SetFromSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$html of method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:addSection\\(\\) expects string, string\\|null given\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/src/ConceptPrint/Section/ConceptSection.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$concept of class App\\\\ConceptPrint\\\\Section\\\\ConceptSection constructor expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LearningPathSection.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$func of method Doctrine\\\\Common\\\\Collections\\\\Collection\\<\\(int\\|string\\),App\\\\Entity\\\\Concept\\|null\\>\\:\\:map\\(\\) expects Closure\\(App\\\\Entity\\\\Concept\\|null\\)\\: string, Closure\\(App\\\\Entity\\\\Concept\\)\\: string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LearningPathSection.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$html of method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:convertHtmlToLatex\\(\\) expects string, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LearningPathSection.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$childNodes on DOMElement\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method removeChild\\(\\) on DOMNode\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method replaceChild\\(\\) on DOMNode\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:addSection\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:convertHtmlToLatex\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has parameter \\$replaceInfo with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has parameter \\$replacement with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function md5 expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:caution\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:choice\\(\\) has parameter \\$choices with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:choice\\(\\) has parameter \\$default with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:error\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:listing\\(\\) has parameter \\$elements with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:note\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:success\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:table\\(\\) has parameter \\$headers with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:table\\(\\) has parameter \\$rows with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:text\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:warning\\(\\) has parameter \\$message with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:canObjectBeRemoved\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:storeChange\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on Symfony\\\\Component\\\\Form\\\\FormInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnalyticsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Annotation\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\AnnotationController\\:\\:validate\\(\\) has parameter \\$object with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$context of method App\\\\Entity\\\\Annotation\\:\\:setContext\\(\\) expects string, bool\\|float\\|int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, bool\\|float\\|int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$selectedText of method App\\\\Entity\\\\Annotation\\:\\:setSelectedText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\Annotation\\:\\:setText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\AnnotationComment\\:\\:setText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$user of method App\\\\Entity\\\\Annotation\\:\\:setUser\\(\\) expects App\\\\Entity\\\\User\\|null, Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$visibility of method App\\\\Entity\\\\Annotation\\:\\:setVisibility\\(\\) expects string, bool\\|float\\|int\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Symfony\\\\Component\\\\PasswordHasher\\\\PasswordHasherInterface\\:\\:hash\\(\\) invoked with 2 parameters, 1 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AuthenticationController.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Symfony\\\\Component\\\\PasswordHasher\\\\PasswordHasherInterface\\:\\:verify\\(\\) invoked with 3 parameters, 2 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AuthenticationController.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\:\\:getName\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method isInstance\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$base of closure expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ContributorController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$relationType of method App\\\\Entity\\\\ConceptRelation\\:\\:setRelationType\\(\\) expects App\\\\Entity\\\\RelationType\\|null, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DataController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function mb_convert_encoding expects array\\<int, string\\>\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DataController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on class\\-string\\|object\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:findId\\(\\) has parameter \\$entry with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:mapArrayById\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:mapArrayById\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:splitUrlLocation\\(\\) has parameter \\$urls with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:splitUrlLocation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:urlRescan\\(\\) has parameter \\$url with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int does not exist on array\\<App\\\\Entity\\\\Concept\\>\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int does not exist on array\\<App\\\\Entity\\\\Contributor\\>\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int does not exist on array\\<App\\\\Entity\\\\ExternalResource\\>\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int does not exist on array\\<App\\\\Entity\\\\LearningOutcome\\>\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int does not exist on array\\<App\\\\Entity\\\\LearningPath\\>\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function array_filter expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\Concept\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\Contributor\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\ExternalResource\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\LearningOutcome\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\LearningPath\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method object\\:\\:getId\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ElFinderController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\ElFinderController\\:\\:forwardToElFinder\\(\\) has parameter \\$query with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ElFinderController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ExternalResourceController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$date of method Symfony\\\\Component\\\\HttpFoundation\\\\Response\\:\\:setLastModified\\(\\) expects DateTimeInterface\\|null, DateTime\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/LatexController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/LearningOutcomeController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/LearningPathController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getEmails\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getUsers\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method removeEmail\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method removeUser\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$object of method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:remove\\(\\) expects object, App\\\\Entity\\\\UserGroupEmail\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns array so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns false so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns null so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:getProjectPath\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of class App\\\\ConceptPrint\\\\Base\\\\ConceptPrint constructor expects string, array\\|string\\|false\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\ConceptPrint\\\\Base\\\\ConceptPrint\\:\\:addIntroduction\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\ConceptPrint\\\\Base\\\\ConceptPrint\\:\\:setHeader\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$studyArea of method App\\\\Controller\\\\PrintController\\:\\:getProjectPath\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/ResourceController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ReviewController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:createResult\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:createResult\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:filterSortData\\(\\) has parameter \\$element with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:groupAnnotationsByConcept\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:groupAnnotationsByConcept\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:searchData\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:sortSearchData\\(\\) has parameter \\$a with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:sortSearchData\\(\\) has parameter \\$b with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$owner of method App\\\\Entity\\\\StudyArea\\:\\:setOwner\\(\\) expects App\\\\Entity\\\\User, Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$default of method Symfony\\\\Component\\\\HttpFoundation\\\\InputBag\\<string\\>\\:\\:get\\(\\) expects string\\|null, false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/TagController.php',
];
$ignoreErrors[] = [
	// identifier: cast.string
	'message' => '#^Cannot cast Symfony\\\\Component\\\\Validator\\\\ConstraintViolationInterface to string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TrackingController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$date of method Symfony\\\\Component\\\\HttpFoundation\\\\Response\\:\\:setLastModified\\(\\) expects DateTimeInterface\\|null, DateTime\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/UploadsController.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getUserIdentifier\\(\\) on Symfony\\\\Component\\\\Security\\\\Core\\\\Authentication\\\\Token\\\\TokenInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Database/SoftDeletableSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Database\\\\SoftDeletableSubscriber\\:\\:preSoftDelete\\(\\) has parameter \\$args with generic class Doctrine\\\\Persistence\\\\Event\\\\LifecycleEventArgs but does not specify its types\\: TObjectManager$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Database/SoftDeletableSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'_route\' on non\\-empty\\-array\\|true\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'_studyArea\' on non\\-empty\\-array\\|true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:duplicate\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:matchPath\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:updateDataAttributes\\(\\) has parameter \\$source with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'concept\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'learningOutcome\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'learningPath\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\LearningOutcome\\:\\:setText\\(\\) expects string, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getComments\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getConceptId\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getUserId\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:visibilityOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$comments with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getUserId\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function assert\\(\\) with false will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getLastUpdated\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getLastUpdatedBy\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: instanceof.alwaysFalse
	'message' => '#^Instanceof between App\\\\Entity\\\\Data\\\\DataInterface and App\\\\Entity\\\\Data\\\\BaseDataTextObject will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: instanceof.trait
	'message' => '#^Instanceof between App\\\\Entity\\\\Data\\\\DataInterface and trait App\\\\Entity\\\\Data\\\\BaseDataTextObject will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:checkEntityRelations\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:doFixConceptRelationOrder\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:doFixConceptRelationOrder\\(\\) has parameter \\$values with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:filterDataOn\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:filterDataOn\\(\\) has parameter \\$results with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:fixConceptRelationOrder\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:fixConceptRelationReferences\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getContributors\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getDotronConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getExternalResources\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getIncomingRelations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastEditInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLearningOutcomes\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getOutgoingRelations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getPriorKnowledge\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getPriorKnowledgeOf\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getRelations\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getTags\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:setDotronConfig\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: varTag.trait
	'message' => '#^PHPDoc tag @var for variable \\$entity has invalid type App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$contributors with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$dotronConfig type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$externalResources with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$incomingRelations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$learningOutcomes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$outgoingRelations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$priorKnowledge with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$priorKnowledgeOf with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$tags with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getDotronConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:setDotronConfig\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$dotronConfig type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\:\\:setStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contracts/ReviewableInterface.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\Contracts\\\\SearchableInterface\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contracts/SearchableInterface.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataExamples\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataExamples\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getContent\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getShortName\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\LearningPathElement\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:OrderElements\\(\\) has parameter \\$elements with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:OrderElements\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getElements\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getElementsOrdered\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$elements with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningPathElement\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\LearningPathElement\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Listener\\\\UserListener\\:\\:updateStudyAreaRights\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Listener/UserListener.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getOriginContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getPath\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getPathContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getSessionId\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getTimestamp\\(\\) should return DateTime but returns DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getUserId\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:setOriginContext\\(\\) has parameter \\$originContext with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:setPathContext\\(\\) has parameter \\$pathContext with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\PageLoad\\:\\:\\$originContext type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\PageLoad\\:\\:\\$pathContext type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:duplicate\\(\\) has parameter \\$changedFields with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getChangedFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getReviewComments\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:orderChangedFields\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:setChangedFields\\(\\) has parameter \\$changedFields with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:setReviewComments\\(\\) has parameter \\$reviewComments with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:validateObjectId\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:validateObjectId\\(\\) has parameter \\$payload with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function array_intersect expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function usort expects TArray of array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_intersect expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$changedFields type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$payload \\(string\\|null\\) does not accept string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$reviewComments type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getPendingChanges\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$pendingChanges with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getLastUpdated\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getLastUpdatedBy\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: generics.lessTypes
	'message' => '#^Generic type Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\> in PHPDoc tag @return does not specify all template types of interface Doctrine\\\\Common\\\\Collections\\\\Selectable\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: generics.lessTypes
	'message' => '#^Generic type Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\> in PHPDoc tag @var for property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups does not specify all template types of interface Doctrine\\\\Common\\\\Collections\\\\Selectable\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getDotronConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastEditInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getRelationTypes\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getUserGroups\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\ReadableCollection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:setDotronConfig\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:validateObject\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysFalse
	'message' => '#^Negated boolean expression is always false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: varTag.trait
	'message' => '#^PHPDoc tag @var for variable \\$entity has invalid type App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$abbreviations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$contributors with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$dotronConfig type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$externalResources with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$learningOutcomes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$learningPaths with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$relationTypes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$tags with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups \\(Doctrine\\\\Common\\\\Collections\\\\Collection&Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\>&iterable\\<App\\\\Entity\\\\UserGroup\\>\\) does not accept Doctrine\\\\Common\\\\Collections\\\\ArrayCollection\\<\\*NEVER\\*, \\*NEVER\\*\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getStudyAreas\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$studyAreas with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getStylings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:setStylings\\(\\) has parameter \\$stylings with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$stylings type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getEvent\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getSessionId\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getTimestamp\\(\\) should return DateTime but returns DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getUserId\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:setContext\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\TrackingEvent\\:\\:\\$context type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:__unserialize\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getAnnotations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getDisplayName\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getSecurityRoles\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getUserGroups\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getUserIdentifier\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:setSecurityRoles\\(\\) has parameter \\$securityRoles with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: property.phpDocType
	'message' => '#^PHPDoc tag @var for property App\\\\Entity\\\\User\\:\\:\\$securityRoles with type array\\[string\\] is not subtype of native type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$address of class Symfony\\\\Component\\\\Mime\\\\Address constructor expects string, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$annotations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$securityRoles type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$userGroups with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserApiToken\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserApiToken\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getFilterState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:setFilterState\\(\\) has parameter \\$filterState with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$filterState type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getEmails\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getGroupTypes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getUsers\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$emails with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$users with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Entity\\\\UserGroupEmail\\:\\:getUserGroup\\(\\) should return App\\\\Entity\\\\UserGroup but returns App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroupEmail.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserProto\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Entity\\\\UserProto\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: doctrine.columnType
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method addOutgoingRelation\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method storeChange\\(\\) on App\\\\Review\\\\ReviewService\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\EntityHandler\\\\ConceptEntityHandler\\:\\:update\\(\\) has parameter \\$originalIncomingRelations with generic class Doctrine\\\\Common\\\\Collections\\\\ArrayCollection but does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\EntityHandler\\\\ConceptEntityHandler\\:\\:update\\(\\) has parameter \\$originalOutgoingRelations with generic class Doctrine\\\\Common\\\\Collections\\\\ArrayCollection but does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:storeChange\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method storeChange\\(\\) on App\\\\Review\\\\ReviewService\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/RelationTypeHandler.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/RelationTypeHandler.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDefaultTagFilter\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/TagHandler.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method setDefaultTagFilter\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/TagHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellBooleanValue\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellDateTime\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellTranslatedValue\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellValue\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getAccessType\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getCreatedAt\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getLastEditInfo\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getOwner\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addDetailedConceptOverviewSheet\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addDetailedRelationshipsOverviewSheet\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralConceptStatisticsSheet\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralInfoSheet\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralRelationshipStatisticsSheet\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$spreadsheet of method App\\\\Excel\\\\SpreadsheetHelper\\:\\:createSheet\\(\\) expects PhpOffice\\\\PhpSpreadsheet\\\\Spreadsheet, PhpOffice\\\\PhpSpreadsheet\\\\Spreadsheet\\|null given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:\\$concepts \\(Doctrine\\\\Common\\\\Collections\\\\Collection&iterable\\<App\\\\Entity\\\\Concept\\>\\) does not accept array\\<App\\\\Entity\\\\Concept\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:\\$relationTypes \\(Doctrine\\\\Common\\\\Collections\\\\Collection&iterable\\<App\\\\Entity\\\\RelationType\\>\\) does not accept array\\<int, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:\\$relationTypes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Excel\\\\TrackingExportBuilder\\:\\:mapContextElements\\(\\) has parameter \\$context with no value type specified in iterable type iterable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Excel\\\\TrackingExportBuilder\\:\\:mapContextElements\\(\\) has parameter \\$contextMap with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\ExceptionHandler\\\\Subscriber\\\\ExceptionSubscriber\\:\\:onKernelException\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ExceptionHandler/Subscriber/ExceptionSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Export\\\\ExportService\\:\\:getChoices\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Export\\\\ExportService\\:\\:getPreviews\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getCamelizedName\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getOwner\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$concept of method App\\\\Export\\\\Provider\\\\RdfProvider\\:\\:generateConceptResourceUrl\\(\\) expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/src/Export/Provider/RelationProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between DateTime and null will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	// identifier: method.unresolvableReturnType
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationsType.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getConcepts\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getRelationTypes\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\ContributorRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\ExternalResourceRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\TagRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Form\\\\Data\\\\DuplicateType\\:\\:checkConcepts\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Form\\\\Data\\\\DuplicateType\\:\\:checkNewStudyArea\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$callback of function array_filter expects \\(callable\\(object\\)\\: bool\\)\\|null, Closure\\(App\\\\Entity\\\\StudyArea\\)\\: bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	// identifier: method.unresolvableReturnType
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Extension/Select2Extension.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method setLearningPath\\(\\) on object\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementType.php',
];
$ignoreErrors[] = [
	// identifier: method.unresolvableReturnType
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementsType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Form\\\\Review\\\\AbstractReviewType\\:\\:addReviewer\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/AbstractReviewType.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	// identifier: method.unresolvableReturnType
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between null and App\\\\Entity\\\\PendingChange will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Form\\\\Review\\\\ReviewDiff\\\\AbstractReviewDiffType\\:\\:getPendingChange\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/AbstractReviewDiffType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewCheckboxDiffType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewRelationDiffType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewSimpleListDiffType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewTextDiffType.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionObjectFooterType.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Form\\\\Review\\\\ReviewSubmissionType\\:\\:getFormTypeForField\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionType.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, array\\<int, string\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/EmailListType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedConceptNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedConceptNames.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedLearningOutcomeNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedLearningOutcomeNames.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedNames.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedNamesInterface\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedNamesInterface.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Naming\\\\NamingService\\:\\:getCached\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/NamingService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\AbbreviationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AbbreviationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\AbbreviationRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AbbreviationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\AnnotationCommentRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationCommentRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\AnnotationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\AnnotationRepository\\:\\:getCountsForUserInStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\AnnotationRepository\\:\\:getForUserAndStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$studyArea of method App\\\\Repository\\\\AnnotationRepository\\:\\:getVisibilityWhere\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ConceptRelationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ConceptRelationRepository uses generic trait Drenso\\\\Shared\\\\Database\\\\RepositoryTraits\\\\FindIdsTrait but does not specify its types\\: T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Repository\\\\ConceptRelationRepository\\:\\:getByRelationTypeCount\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Repository\\\\ConceptRelationRepository\\:\\:getCountForStudyArea\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ConceptRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ConceptRepository uses generic trait Drenso\\\\Shared\\\\Database\\\\RepositoryTraits\\\\FindIdsTrait but does not specify its types\\: T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:getCountForStudyArea\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:loadRelations\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:preLoadData\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ContributorRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContributorRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\ContributorRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContributorRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\ContributorRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContributorRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\Data\\\\DataExamplesRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/Data/DataExamplesRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\Data\\\\DataHowToRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/Data/DataHowToRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\Data\\\\DataIntroductionRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/Data/DataIntroductionRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\Data\\\\DataSelfAssessmentRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/Data/DataSelfAssessmentRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\Data\\\\DataTheoryExplanationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/Data/DataTheoryExplanationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ExternalResourceRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ExternalResourceRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\ExternalResourceRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ExternalResourceRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\ExternalResourceRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ExternalResourceRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\HelpRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/HelpRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\LearningOutcomeRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findUnusedNumberInStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findUsedConceptIdsForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\LearningPathElementRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathElementRepository.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\LearningPathRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\LearningPathRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\LearningPathRepository\\:\\:removeElementBasedOnConcept\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\PageLoadRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PageLoadRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\PageLoadRepository\\:\\:purgeForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PageLoadRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\PendingChangeRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PendingChangeRepository.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Repository\\\\PendingChangeRepository\\:\\:getForUser\\(\\) should return array\\<App\\\\Entity\\\\PendingChange\\> but returns array\\<int, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PendingChangeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\PendingChangeRepository\\:\\:getMultiple\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PendingChangeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\RelationTypeRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/RelationTypeRepository.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:flush\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/RelationTypeRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\ReviewRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ReviewRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\StudyAreaFieldConfigurationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaFieldConfigurationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\StudyAreaGroupRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaGroupRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\StudyAreaRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\StudyAreaRepository\\:\\:getOwnerAmount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\StudyAreaRepository\\:\\:getVisibleCount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\StylingConfigurationRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StylingConfigurationRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\TagRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TagRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\TagRepository\\:\\:findForStudyArea\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TagRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\TagRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TagRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\TrackingEventRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TrackingEventRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\TrackingEventRepository\\:\\:purgeForStudyArea\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TrackingEventRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserApiTokenRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserApiTokenRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserBrowserStateRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserBrowserStateRepository.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Repository\\\\UserBrowserStateRepository\\:\\:findForUser\\(\\) should return App\\\\Entity\\\\UserBrowserState\\|null but returns object\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserBrowserStateRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserGroupEmailRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserGroupEmailRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserGroupRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserGroupRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Repository\\\\UserGroupRepository\\:\\:removeObsoleteGroups\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserGroupRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserProtoRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserProtoRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Repository\\\\UserProtoRepository\\:\\:getForEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserProtoRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Repository\\\\UserRepository extends generic class Doctrine\\\\Bundle\\\\DoctrineBundle\\\\Repository\\\\ServiceEntityRepository but does not specify its types\\: TEntityClass$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getFallbackUsers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getUserForEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getUsersForEmails\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getName\\(\\) on ReflectionType\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.leftAlwaysTrue
	'message' => '#^Left side of && is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property App\\\\Request\\\\Subscriber\\\\RequestStudyAreaSubscriber\\:\\:\\$studyArea \\(App\\\\Entity\\\\StudyArea\\|null\\) does not accept object\\|null\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Request\\\\Wrapper\\\\RequestStudyArea\\:\\:getStudyAreaId\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Wrapper/RequestStudyArea.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getId\\(\\) on an unknown class App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getReviewName\\(\\) on an unknown class App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	// identifier: parameter.trait
	'message' => '#^Parameter \\$reviewable of method App\\\\Review\\\\Exception\\\\IncompatibleChangeException\\:\\:__construct\\(\\) has invalid type App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	// identifier: constructor.unusedParameter
	'message' => '#^Constructor of class App\\\\Review\\\\Exception\\\\IncompatibleChangeMergeException has an unused parameter \\$merge\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeMergeException.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function array_intersect expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/OverlappingFieldsChangedException.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_intersect expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/OverlappingFieldsChangedException.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Model/PendingChangeObjectInfo.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_merge expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Model/PendingChangeObjectInfo.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getSession\\(\\) on null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method setObject\\(\\) on object\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:asSimpleType\\(\\) has parameter \\$value with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:asSimpleType\\(\\) should return string\\|false\\|null but returns float\\|int\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:createReview\\(\\) has parameter \\$markedChanges with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:determineChangedFieldsFromSnapshot\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:flush\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: property.phpDocType
	'message' => '#^PHPDoc tag @var for property App\\\\Review\\\\ReviewService\\:\\:\\$serializer with type JMS\\\\Serializer\\\\SerializerInterface\\|null is not subtype of native type JMS\\\\Serializer\\\\Serializer\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$className of method Doctrine\\\\ORM\\\\EntityManagerInterface\\:\\:getRepository\\(\\) expects class\\-string\\<object\\>, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arrays of function array_merge expects array, array\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$object of method App\\\\Review\\\\ReviewService\\:\\:isReviewModeEnabledForObject\\(\\) expects App\\\\Entity\\\\Contracts\\\\ReviewableInterface, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$originalSnapshot of method App\\\\Review\\\\ReviewService\\:\\:determineChangedFieldsFromSnapshot\\(\\) expects string, string\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between App\\\\Entity\\\\PendingChange and \'30_remove\' will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between null and string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type T in call to method Doctrine\\\\ORM\\\\EntityManagerInterface\\:\\:getRepository\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$name with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$parameters with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$referenceType with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generateBrowserUrl\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:getContext\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:getRouteCollection\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:match\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:match\\(\\) has parameter \\$pathinfo with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:setContext\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'path\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Http/Authentication/AuthenticationSuccessHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Security\\\\Http\\\\Authentication\\\\AuthenticationSuccessHandler\\:\\:__construct\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Http/Authentication/AuthenticationSuccessHandler.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getEmail\\(\\) on App\\\\Entity\\\\UserGroupEmail\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getUserIdentifier\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Security\\\\UserPermissions\\:\\:addPermissionFromGroup\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\:\\:setLastUsed\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\:\\:update\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Serializer\\\\Handler\\\\LearningPathVisualisationResultHandler\\:\\:getSubscribedEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Serializer/Handler/LearningPathVisualisationResultHandler.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysFalse
	'message' => '#^Negated boolean expression is always false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Serializer/Handler/LearningPathVisualisationResultHandler.php',
];
$ignoreErrors[] = [
	// identifier: offsetAssign.dimType
	'message' => '#^Cannot assign offset \'sFirst\'\\|\'sLast\'\\|\'sNext\'\\|\'sPrevious\'\\|\'sSortAscending\'\\|\'sSortDescending\' to array\\<string, string\\>\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: ternary.elseUnreachable
	'message' => '#^Else branch is unreachable because ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:dataTable\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:dataTable\\(\\) has parameter \\$tableId with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:getDefaultDataTableOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:getDutchDataTableTranslation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) has parameter \\$search with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) has parameter \\$text with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\LtbRouterExtension\\:\\:browserPath\\(\\) has parameter \\$name with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LtbRouterExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\LtbRouterExtension\\:\\:browserPath\\(\\) has parameter \\$parameters with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LtbRouterExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Twig\\\\TranslationStringExtension\\:\\:trString\\(\\) has parameter \\$text with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/TranslationStringExtension.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\Model\\\\Url\\:\\:getUrlParts\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/Url.php',
];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between string and null will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.variable
	'message' => '#^Variable \\$id on left side of \\?\\? always exists and is not nullable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.variable
	'message' => '#^Variable \\$path on left side of \\?\\? always exists and is not nullable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:cacheUrl\\(\\) has parameter \\$expiry with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:checkAllUrls\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:checkStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:findBadUrls\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:getUrlsForStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\UrlUtils\\\\UrlScanner\\:\\:_scanText\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlScanner.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$id of class App\\\\UrlUtils\\\\Model\\\\UrlContext constructor expects int, int\\|null given\\.$#',
	'count' => 12,
	'path' => __DIR__ . '/src/UrlUtils/UrlScanner.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/Validator/Constraint/ConceptRelationValidator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Validator\\\\Constraint\\\\ConceptRelationValidator\\:\\:\\$violations type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/Constraint/ConceptRelationValidator.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:scanTextProvider\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:testScanText\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:testScanText\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
