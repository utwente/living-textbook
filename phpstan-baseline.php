<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method modify\\(\\) on DateTimeImmutable\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Analytics\\\\AnalyticsService\\:\\:build\\(\\) has parameter \\$settings with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Analytics\\\\AnalyticsService\\:\\:firstFromFinder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Analytics\\\\AnalyticsService\\:\\:retrieveConceptNamesExport\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Analytics\\\\AnalyticsService\\:\\:retrieveTrackingDataExport\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Entity\\\\PageLoad\\:\\:setStudyArea\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$timestamp of method App\\\\Entity\\\\PageLoad\\:\\:setTimestamp\\(\\) expects DateTime, DateTime\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$content of method Symfony\\\\Component\\\\Filesystem\\\\Filesystem\\:\\:dumpFile\\(\\) expects resource\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Analytics/AnalyticsService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeImmutable\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Analytics\\\\Model\\\\SynthesizeRequest\\:\\:getSettings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Analytics\\\\Model\\\\SynthesizeRequest\\:\\:validate\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Analytics/Model/SynthesizeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access constant class on Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null\\.$#',
	'identifier' => 'classConstant.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:createDataResponse\\(\\) has parameter \\$extraData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:createDataResponse\\(\\) has parameter \\$serializationGroups with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:getArrayFromBody\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/AbstractApiController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(object\\)\\: mixed\\)\\|null, Closure\\(App\\\\Entity\\\\Concept\\)\\: App\\\\Entity\\\\Concept given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objects of class Drenso\\\\Shared\\\\IdMap\\\\IdMap constructor expects array\\<Drenso\\\\Shared\\\\Interfaces\\\\IdInterface\\>, array\\<object\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$requestTag in PHPDoc tag @var does not match assigned variable \\$requestTags\\.$#',
	'identifier' => 'varTag.differentVariable',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objects of class Drenso\\\\Shared\\\\IdMap\\\\IdMap constructor expects array\\<Drenso\\\\Shared\\\\Interfaces\\\\IdInterface\\>, array\\<object\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$object of method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:assertStudyAreaObject\\(\\) expects App\\\\Entity\\\\Contracts\\\\StudyAreaFilteredInterface, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/src/Api/Controller/ConceptRelationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$object of method App\\\\Api\\\\Controller\\\\AbstractApiController\\:\\:assertStudyAreaObject\\(\\) expects App\\\\Entity\\\\Contracts\\\\StudyAreaFilteredInterface, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/src/Api/Controller/StylingConfigurationRelationOverrideController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$outgoingRelations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\ConceptApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:\\$definition on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\ConceptApiModel\\:\\:\\$synonyms on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\ConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/ConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\Detailed\\\\DetailedConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Detailed/DetailedConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\LayoutConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$layouts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\LayoutConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$overrides with generic class Drenso\\\\Shared\\\\IdMap\\\\IdMap but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\LayoutConfigurationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\LayoutConfigurationApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel\\:\\:getOverride\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel\\:\\:mapToEntity\\(\\) should return App\\\\Entity\\\\LayoutConfigurationOverride but returns App\\\\Entity\\\\Override\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$concept of class App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$layoutConfiguration of class App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$override of class App\\\\Api\\\\Model\\\\LayoutConfigurationOverrideApiModel constructor expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/LayoutConfigurationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\RelationTypeApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/RelationTypeApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\RelationTypeApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/RelationTypeApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefaultLayoutConfiguration\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefaultStylingConfiguration\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\StudyAreaApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\StudyAreaApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StudyAreaApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$conceptOverrides with generic class Drenso\\\\Shared\\\\IdMap\\\\IdMap but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$relationOverrides with generic class Drenso\\\\Shared\\\\IdMap\\\\IdMap but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationApiModel\\:\\:__construct\\(\\) has parameter \\$stylings with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\StylingConfigurationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\StylingConfigurationApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel\\:\\:getOverride\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel\\:\\:mapToEntity\\(\\) should return App\\\\Entity\\\\StylingConfigurationConceptOverride but returns App\\\\Entity\\\\Override\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$concept of class App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$stylingConfiguration of class App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$override of class App\\\\Api\\\\Model\\\\StylingConfigurationConceptOverrideApiModel constructor expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationConceptOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel\\:\\:getOverride\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel\\:\\:mapToEntity\\(\\) should return App\\\\Entity\\\\StylingConfigurationRelationOverride but returns App\\\\Entity\\\\Override\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$relation of class App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$stylingConfiguration of class App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$override of class App\\\\Api\\\\Model\\\\StylingConfigurationRelationOverrideApiModel constructor expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/StylingConfigurationRelationOverrideApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\TagApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/TagApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\TagApiModel\\:\\:\\$color on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/TagApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Api\\\\Model\\\\TagApiModel\\:\\:\\$name on left side of \\?\\? is not nullable nor uninitialized\\.$#',
	'identifier' => 'nullCoalesce.initializedProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/TagApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDotronConfig\\(\\) on App\\\\Entity\\\\ConceptRelation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getRelationType\\(\\) on App\\\\Entity\\\\ConceptRelation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:__construct\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$sourceId of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$targetId of class App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$id is already assigned\\.$#',
	'identifier' => 'assign.readOnlyProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$sourceId is already assigned\\.$#',
	'identifier' => 'assign.readOnlyProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Readonly property App\\\\Api\\\\Model\\\\Update\\\\UpdateConceptRelationApiModel\\:\\:\\$targetId is already assigned\\.$#',
	'identifier' => 'assign.readOnlyProperty',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Update/UpdateConceptRelationApiModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Api\\\\Model\\\\Validation\\\\ValidationError has an uninitialized readonly property \\$message\\. Assign it in the constructor\\.$#',
	'identifier' => 'property.uninitializedReadonly',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationError.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Api\\\\Model\\\\Validation\\\\ValidationError has an uninitialized readonly property \\$propertyPath\\. Assign it in the constructor\\.$#',
	'identifier' => 'property.uninitializedReadonly',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationError.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Model\\\\Validation\\\\ValidationFailedData\\:\\:__construct\\(\\) has parameter \\$violations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Model/Validation/ValidationFailedData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Security\\\\ApiAuthenticator\\:\\:createToken\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Security/ApiAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Api\\\\Security\\\\ApiAuthenticator\\:\\:onAuthenticationSuccess\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Security/ApiAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Attribute\\\\DenyOnFrozenStudyArea\\:\\:__construct\\(\\) has parameter \\$routeParams with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Attribute/DenyOnFrozenStudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function assert\\(\\) with true will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/AddAccountCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between Symfony\\\\Component\\\\Console\\\\Helper\\\\QuestionHelper and Symfony\\\\Component\\\\Console\\\\Helper\\\\QuestionHelper will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/AddAccountCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAddress\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 5,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 11,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:reviewRequested\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionApproved\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionDenied\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:submissionPublished\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\Notification\\\\ReviewNotificationService\\:\\:trans\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/Notification/ReviewNotificationService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Communication\\\\SetFromSubscriber\\:\\:onMessage\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Communication/SetFromSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$html of method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:addSection\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/src/ConceptPrint/Section/ConceptSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$concept of class App\\\\ConceptPrint\\\\Section\\\\ConceptSection constructor expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LearningPathSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$func of method Doctrine\\\\Common\\\\Collections\\\\Collection\\<\\(int\\|string\\),App\\\\Entity\\\\Concept\\|null\\>\\:\\:map\\(\\) expects Closure\\(App\\\\Entity\\\\Concept\\|null\\)\\: string, Closure\\(App\\\\Entity\\\\Concept\\)\\: string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LearningPathSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$childNodes on DOMElement\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method removeChild\\(\\) on DOMNode\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method replaceChild\\(\\) on DOMNode\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:addSection\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:convertHtmlToLatex\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has parameter \\$replaceInfo with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ConceptPrint\\\\Section\\\\LtbSection\\:\\:replacePlaceholder\\(\\) has parameter \\$replacement with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function md5 expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/ConceptPrint/Section/LtbSection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:caution\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:choice\\(\\) has parameter \\$choices with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:choice\\(\\) has parameter \\$default with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:error\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:listing\\(\\) has parameter \\$elements with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:note\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:success\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:table\\(\\) has parameter \\$headers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:table\\(\\) has parameter \\$rows with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:text\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\NullStyle\\:\\:warning\\(\\) has parameter \\$message with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Console/NullStyle.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:canObjectBeRemoved\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:storeChange\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AbbreviationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on Symfony\\\\Component\\\\Form\\\\FormInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnalyticsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Annotation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\AnnotationController\\:\\:validate\\(\\) has parameter \\$object with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$context of method App\\\\Entity\\\\Annotation\\:\\:setContext\\(\\) expects string, bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$selectedText of method App\\\\Entity\\\\Annotation\\:\\:setSelectedText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\Annotation\\:\\:setText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\AnnotationComment\\:\\:setText\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method App\\\\Entity\\\\Annotation\\:\\:setUser\\(\\) expects App\\\\Entity\\\\User\\|null, Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$visibility of method App\\\\Entity\\\\Annotation\\:\\:setVisibility\\(\\) expects string, bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/AnnotationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Symfony\\\\Component\\\\PasswordHasher\\\\PasswordHasherInterface\\:\\:hash\\(\\) invoked with 2 parameters, 1 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AuthenticationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Symfony\\\\Component\\\\PasswordHasher\\\\PasswordHasherInterface\\:\\:verify\\(\\) invoked with 3 parameters, 2 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/AuthenticationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\:\\:getName\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isInstance\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$base of closure expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ConceptController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ContributorController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on class\\-string\\|object\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:createStudyAreaForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:findId\\(\\) has parameter \\$entry with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:mapArrayById\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:mapArrayById\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:splitUrlLocation\\(\\) has parameter \\$urls with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:splitUrlLocation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\DefaultController\\:\\:urlRescan\\(\\) has parameter \\$url with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<App\\\\Entity\\\\Concept\\>\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<App\\\\Entity\\\\Contributor\\>\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<App\\\\Entity\\\\ExternalResource\\>\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<App\\\\Entity\\\\LearningOutcome\\>\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<App\\\\Entity\\\\LearningPath\\>\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_filter expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\Concept\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\Contributor\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\ExternalResource\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\LearningOutcome\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\<App\\\\Entity\\\\LearningPath\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/DefaultController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\ElFinderController\\:\\:forwardToElFinder\\(\\) has parameter \\$query with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ElFinderController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ExternalResourceController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method Symfony\\\\Component\\\\HttpFoundation\\\\Response\\:\\:setLastModified\\(\\) expects DateTimeInterface\\|null, DateTime\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/LatexController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/LearningOutcomeController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/LearningPathController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmails\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUsers\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method removeEmail\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method removeUser\\(\\) on App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:remove\\(\\) expects object, App\\\\Entity\\\\UserGroupEmail\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PermissionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function assert\\(\\) with true will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between Bobv\\\\LatexBundle\\\\Exception\\\\ImageNotFoundException and Bobv\\\\LatexBundle\\\\Exception\\\\ImageNotFoundException will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between Bobv\\\\LatexBundle\\\\Exception\\\\LatexException and Bobv\\\\LatexBundle\\\\Exception\\\\LatexException will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns array so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns false so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:filename\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\PrintController\\:\\:getProjectPath\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of class App\\\\ConceptPrint\\\\Base\\\\ConceptPrint constructor expects string, array\\|string\\|false\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\ConceptPrint\\\\Base\\\\ConceptPrint\\:\\:addIntroduction\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\ConceptPrint\\\\Base\\\\ConceptPrint\\:\\:setHeader\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$studyArea of method App\\\\Controller\\\\PrintController\\:\\:getProjectPath\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/PrintController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\!\\=\\= between DateTime and null will always evaluate to true\\.$#',
	'identifier' => 'notIdentical.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/RelationTypeController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/ResourceController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ReviewController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:createResult\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:createResult\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:filterSortData\\(\\) has parameter \\$element with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:groupAnnotationsByConcept\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:groupAnnotationsByConcept\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:searchData\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:sortSearchData\\(\\) has parameter \\$a with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Controller\\\\SearchController\\:\\:sortSearchData\\(\\) has parameter \\$b with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/SearchController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$owner of method App\\\\Entity\\\\StudyArea\\:\\:setOwner\\(\\) expects App\\\\Entity\\\\User, Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$default of method Symfony\\\\Component\\\\HttpFoundation\\\\InputBag\\<string\\>\\:\\:get\\(\\) expects string\\|null, false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/StudyAreaController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/TagController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast Symfony\\\\Component\\\\Validator\\\\ConstraintViolationInterface to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TrackingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method Symfony\\\\Component\\\\HttpFoundation\\\\Response\\:\\:setLastModified\\(\\) expects DateTimeInterface\\|null, DateTime\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/UploadsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'_route\' on non\\-empty\\-array\\|true\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'_studyArea\' on non\\-empty\\-array\\|true\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:duplicate\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:matchPath\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DuplicationUtils\\\\StudyAreaDuplicator\\:\\:updateDataAttributes\\(\\) has parameter \\$source with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'concept\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'learningOutcome\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'learningPath\' does not exist on array\\{_studyArea\\: int\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type App\\\\Entity\\\\LearningPathElement is not subtype of native type null\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of method App\\\\Entity\\\\LearningOutcome\\:\\:setText\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/DuplicationUtils/StudyAreaDuplicator.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Abbreviation\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Abbreviation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Abbreviation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getComments\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getConceptId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:getUserId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Annotation\\:\\:visibilityOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$comments with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Annotation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\AnnotationComment\\:\\:getUserId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\AnnotationComment\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/AnnotationComment.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function assert\\(\\) with false will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getLastUpdated\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getLastUpdatedBy\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between App\\\\Entity\\\\Data\\\\DataInterface and App\\\\Entity\\\\Data\\\\BaseDataTextObject will always evaluate to false\\.$#',
	'identifier' => 'instanceof.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between App\\\\Entity\\\\Data\\\\DataInterface and trait App\\\\Entity\\\\Data\\\\BaseDataTextObject will always evaluate to false\\.$#',
	'identifier' => 'instanceof.trait',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:checkEntityRelations\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:doFixConceptRelationOrder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:doFixConceptRelationOrder\\(\\) has parameter \\$values with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:filterDataOn\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:filterDataOn\\(\\) has parameter \\$results with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:fixConceptRelationOrder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:fixConceptRelationReferences\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getContributors\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getDotronConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getExternalResources\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getIncomingRelations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastEditInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getLearningOutcomes\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getOutgoingRelations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getPriorKnowledge\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getPriorKnowledgeOf\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getRelations\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:getTags\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:setDotronConfig\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Concept\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$entity has invalid type App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'varTag.trait',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$contributors with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$dotronConfig type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$externalResources with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$incomingRelations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$layoutOverrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$learningOutcomes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$outgoingRelations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$priorKnowledge with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$priorKnowledgeOf with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$stylingOverrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$tags with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Concept\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Concept.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getDotronConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ConceptRelation\\:\\:setDotronConfig\\(\\) has parameter \\$dotronConfig with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$dotronConfig type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$stylingOverrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ConceptRelation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ConceptRelation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\:\\:setStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contracts/ReviewableInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contracts\\\\SearchableInterface\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contracts/SearchableInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Contributor\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Contributor\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Contributor.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataExamples\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataExamples\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataExamples\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataExamples.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataHowTo\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataHowTo.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataIntroduction\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataIntroduction.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataSelfAssessment\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataSelfAssessment.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Data\\\\DataTheoryExplanation\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Data/DataTheoryExplanation.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\ExternalResource\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\ExternalResource\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ExternalResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getContent\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Help\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Help\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Help.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LayoutConfiguration\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LayoutConfiguration\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LayoutConfiguration\\:\\:getLayouts\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LayoutConfiguration\\:\\:setLayouts\\(\\) has parameter \\$layouts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$layouts type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$overrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfiguration\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LayoutConfigurationOverride\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfigurationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfigurationOverride\\:\\:\\$concept type mapping mismatch\\: database can contain App\\\\Entity\\\\Concept\\|null but property expects App\\\\Entity\\\\Concept\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfigurationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LayoutConfigurationOverride\\:\\:\\$layoutConfiguration type mapping mismatch\\: database can contain App\\\\Entity\\\\LayoutConfiguration\\|null but property expects App\\\\Entity\\\\LayoutConfiguration\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LayoutConfigurationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:getShortName\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:searchIn\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningOutcome\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningOutcome\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningOutcome.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\LearningPathElement\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:OrderElements\\(\\) has parameter \\$elements with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:OrderElements\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getElements\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getElementsOrdered\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPath\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$elements with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPath\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPath.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPathElement\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\LearningPathElement\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\LearningPathElement\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/LearningPathElement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function assert\\(\\) with true will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Listener/UserListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between App\\\\Repository\\\\UserGroupEmailRepository\\<App\\\\Entity\\\\UserGroupEmail\\> and App\\\\Repository\\\\UserGroupEmailRepository will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Listener/UserListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Listener\\\\UserListener\\:\\:updateStudyAreaRights\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Listener/UserListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Override\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Override\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Override\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Override\\:\\:getOverride\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Override\\:\\:setOverride\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$override type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Override\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Override.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getOriginContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getPath\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getPathContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getSessionId\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getTimestamp\\(\\) should return DateTime but returns DateTime\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:getUserId\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:setOriginContext\\(\\) has parameter \\$originContext with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PageLoad\\:\\:setPathContext\\(\\) has parameter \\$pathContext with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PageLoad\\:\\:\\$originContext type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PageLoad\\:\\:\\$pathContext type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PageLoad.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:duplicate\\(\\) has parameter \\$changedFields with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getChangedFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getReviewComments\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:orderChangedFields\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:setChangedFields\\(\\) has parameter \\$changedFields with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:setReviewComments\\(\\) has parameter \\$reviewComments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:validateObjectId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\PendingChange\\:\\:validateObjectId\\(\\) has parameter \\$payload with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_intersect expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function usort expects TArray of array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_intersect expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$changedFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$payload \\(string\\|null\\) does not accept string\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$reviewComments type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\PendingChange\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/PendingChange.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\RelationType\\:\\:testChange\\(\\) should return App\\\\Entity\\\\Contracts\\\\ReviewableInterface but returns App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\RelationType\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/RelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getPendingChanges\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Review\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$pendingChanges with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Review\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Review.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getLastUpdated\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getLastUpdatedBy\\(\\) on an unknown class App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Generic type Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\> in PHPDoc tag @return does not specify all template types of interface Doctrine\\\\Common\\\\Collections\\\\Selectable\\: TKey, T$#',
	'identifier' => 'generics.lessTypes',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Generic type Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\> in PHPDoc tag @var for property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups does not specify all template types of interface Doctrine\\\\Common\\\\Collections\\\\Selectable\\: TKey, T$#',
	'identifier' => 'generics.lessTypes',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastEditInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getRelationTypes\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyArea\\:\\:getUserGroups\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\ReadableCollection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always false\\.$#',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$entity has invalid type App\\\\Database\\\\Traits\\\\Blameable\\.$#',
	'identifier' => 'varTag.trait',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$abbreviations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$contributors with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$externalResources with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$learningOutcomes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$learningPaths with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$relationTypes with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$tags with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups \\(Doctrine\\\\Common\\\\Collections\\\\Collection&Doctrine\\\\Common\\\\Collections\\\\Selectable\\<App\\\\Entity\\\\UserGroup\\>&iterable\\<App\\\\Entity\\\\UserGroup\\>\\) does not accept Doctrine\\\\Common\\\\Collections\\\\ArrayCollection\\<\\*NEVER\\*, \\*NEVER\\*\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyArea\\:\\:\\$userGroups with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaFieldConfiguration\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaFieldConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StudyAreaGroup\\:\\:getStudyAreas\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$studyAreas with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StudyAreaGroup\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StudyAreaGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:getStylings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfiguration\\:\\:setStylings\\(\\) has parameter \\$stylings with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$conceptOverrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$relationOverrides is never written, only read\\.$#',
	'identifier' => 'property.onlyRead',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$stylings type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfiguration\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfiguration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfigurationConceptOverride\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationConceptOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfigurationConceptOverride\\:\\:\\$concept type mapping mismatch\\: database can contain App\\\\Entity\\\\Concept\\|null but property expects App\\\\Entity\\\\Concept\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationConceptOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfigurationConceptOverride\\:\\:\\$stylingConfiguration type mapping mismatch\\: database can contain App\\\\Entity\\\\StylingConfiguration\\|null but property expects App\\\\Entity\\\\StylingConfiguration\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationConceptOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\StylingConfigurationRelationOverride\\:\\:__construct\\(\\) has parameter \\$override with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationRelationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfigurationRelationOverride\\:\\:\\$relation type mapping mismatch\\: database can contain App\\\\Entity\\\\ConceptRelation\\|null but property expects App\\\\Entity\\\\ConceptRelation\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationRelationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\StylingConfigurationRelationOverride\\:\\:\\$stylingConfiguration type mapping mismatch\\: database can contain App\\\\Entity\\\\StylingConfiguration\\|null but property expects App\\\\Entity\\\\StylingConfiguration\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/StylingConfigurationRelationOverride.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getConcepts\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\Tag\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$concepts with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\Tag\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Tag.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getEvent\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getSessionId\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getTimestamp\\(\\) should return DateTime but returns DateTime\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:getUserId\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\TrackingEvent\\:\\:setContext\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\TrackingEvent\\:\\:\\$context type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/TrackingEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:__unserialize\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getAnnotations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getDisplayName\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getSecurityRoles\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getUserGroups\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:getUserIdentifier\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\User\\:\\:setSecurityRoles\\(\\) has parameter \\$securityRoles with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for property App\\\\Entity\\\\User\\:\\:\\$securityRoles with type array\\[string\\] is not subtype of native type array\\.$#',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$address of class Symfony\\\\Component\\\\Mime\\\\Address constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$annotations with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$securityRoles type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\User\\:\\:\\$userGroups with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserApiToken\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserApiToken\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserApiToken\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserApiToken.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getFilterState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserBrowserState\\:\\:setFilterState\\(\\) has parameter \\$filterState with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$filterState type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserBrowserState\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserBrowserState.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getEmails\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getGroupTypes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getStudyArea\\(\\) should return App\\\\Entity\\\\StudyArea but returns App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroup\\:\\:getUsers\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedAt \\(DateTime\\) does not accept DateTime\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$deletedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$emails with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserGroup\\:\\:\\$users with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserGroupEmail\\:\\:getUserGroup\\(\\) should return App\\\\Entity\\\\UserGroup but returns App\\\\Entity\\\\UserGroup\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserGroupEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserProto\\:\\:getLastUpdated\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Entity\\\\UserProto\\:\\:getLastUpdatedBy\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$createdBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$updatedAt type mapping mismatch\\: database can contain DateTime\\|null but property expects DateTime\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Entity\\\\UserProto\\:\\:\\$updatedBy type mapping mismatch\\: database can contain string\\|null but property expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/UserProto.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method addOutgoingRelation\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method storeChange\\(\\) on App\\\\Review\\\\ReviewService\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\ConceptEntityHandler\\:\\:update\\(\\) has parameter \\$originalIncomingRelations with generic class Doctrine\\\\Common\\\\Collections\\\\ArrayCollection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\ConceptEntityHandler\\:\\:update\\(\\) has parameter \\$originalOutgoingRelations with generic class Doctrine\\\\Common\\\\Collections\\\\ArrayCollection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Review\\\\ReviewService\\:\\:storeChange\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/ConceptEntityHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\LayoutConfigurationOverrideHandler\\:\\:add\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/LayoutConfigurationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\LayoutConfigurationOverrideHandler\\:\\:delete\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/LayoutConfigurationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\LayoutConfigurationOverrideHandler\\:\\:update\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/LayoutConfigurationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method storeChange\\(\\) on App\\\\Review\\\\ReviewService\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/RelationTypeHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\!\\=\\= between DateTime and null will always evaluate to true\\.$#',
	'identifier' => 'notIdentical.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/RelationTypeHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/RelationTypeHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationConceptOverrideHandler\\:\\:add\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationConceptOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationConceptOverrideHandler\\:\\:delete\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationConceptOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationConceptOverrideHandler\\:\\:update\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationConceptOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationRelationOverrideHandler\\:\\:add\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationRelationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationRelationOverrideHandler\\:\\:delete\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationRelationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\EntityHandler\\\\StylingConfigurationRelationOverrideHandler\\:\\:update\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/StylingConfigurationRelationOverrideHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefaultTagFilter\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/EntityHandler/TagHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setDefaultTagFilter\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/EntityHandler/TagHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellBooleanValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellDateTime\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellTranslatedValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\SpreadsheetHelper\\:\\:setCellValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/SpreadsheetHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAccessType\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCreatedAt\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLastEditInfo\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getOwner\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addDetailedConceptOverviewSheet\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addDetailedRelationshipsOverviewSheet\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralConceptStatisticsSheet\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralInfoSheet\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\StudyAreaStatusBuilder\\:\\:addGeneralRelationshipStatisticsSheet\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$spreadsheet of method App\\\\Excel\\\\SpreadsheetHelper\\:\\:createSheet\\(\\) expects PhpOffice\\\\PhpSpreadsheet\\\\Spreadsheet, PhpOffice\\\\PhpSpreadsheet\\\\Spreadsheet\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/src/Excel/StudyAreaStatusBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\TrackingExportBuilder\\:\\:mapContextElements\\(\\) has parameter \\$context with no value type specified in iterable type iterable\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Excel\\\\TrackingExportBuilder\\:\\:mapContextElements\\(\\) has parameter \\$contextMap with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Excel/TrackingExportBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\ExceptionHandler\\\\Subscriber\\\\ExceptionSubscriber\\:\\:onKernelException\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/ExceptionHandler/Subscriber/ExceptionSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Export\\\\ExportService\\:\\:getChoices\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Export\\\\ExportService\\:\\:getPreviews\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\<float\\|int\\|string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/ExportService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCamelizedName\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFullName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getOwner\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$concept of method App\\\\Export\\\\Provider\\\\RdfProvider\\:\\:generateConceptResourceUrl\\(\\) expects App\\\\Entity\\\\Concept, App\\\\Entity\\\\Concept\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Export/Provider/RdfProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/src/Export/Provider/RelationProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Abbreviation\\\\EditAbbreviationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Abbreviation/EditAbbreviationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Analytics\\\\LearningPathAnalyticsType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Analytics/LearningPathAnalyticsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Analytics\\\\SynthesizeRequestType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Analytics/SynthesizeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Analytics\\\\SynthesizeRequestType\\:\\:addChanceType\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Analytics/SynthesizeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Analytics\\\\SynthesizeRequestType\\:\\:addDaysBetweenType\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Analytics/SynthesizeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Analytics\\\\SynthesizeRequestType\\:\\:addNumberType\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Analytics/SynthesizeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Authentication\\\\LoginType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Authentication/LoginType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\RelationType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Concept\\\\ConceptRelationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Concept\\\\ConceptRelationType\\:\\:addConceptType\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Concept\\\\ConceptRelationType\\:\\:addTextType\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between DateTime and null will always evaluate to false\\.$#',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Concept\\\\ConceptRelationsType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$offset of method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'method.unresolvableReturnType',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Concept/ConceptRelationsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getConcepts\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getRelationTypes\\(\\) on App\\\\Entity\\\\StudyArea\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Concept\\\\EditConceptType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\ContributorRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\ExternalResourceRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Repository\\\\TagRepository\\:\\:findForStudyAreaQb\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Concept/EditConceptType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Contributor\\\\EditContributorType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Contributor/EditContributorType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Data\\\\AbstractBaseDataType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/AbstractBaseDataType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Data\\\\DownloadPreviewType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DownloadPreviewType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Data\\\\DownloadType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DownloadType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Data\\\\DuplicateType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Data\\\\DuplicateType\\:\\:checkConcepts\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Data\\\\DuplicateType\\:\\:checkNewStudyArea\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/DuplicateType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Data\\\\JsonUploadType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Data/JsonUploadType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$offset of method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetExists\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Extension/Select2Extension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$offset of method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Extension/Select2Extension.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'method.unresolvableReturnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Extension/Select2Extension.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\ExternalResource\\\\EditExternalResourceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/ExternalResource/EditExternalResourceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Help\\\\EditHelpType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Help/EditHelpType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningOutcome\\\\EditLearningOutcomeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningOutcome/EditLearningOutcomeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningPath\\\\EditLearningPathType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/EditLearningPathType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningPath\\\\LearningPathElementContainerType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementContainerType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningPath\\\\LearningPathElementSelectorType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementSelectorType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningPath\\\\LearningPathElementType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\LearningPath\\\\LearningPathElementsType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$offset of method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 3,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'method.unresolvableReturnType',
	'count' => 3,
	'path' => __DIR__ . '/src/Form/LearningPath/LearningPathElementsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Permission\\\\AddAdminType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Permission/AddAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Permission\\\\AddPermissionsType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Permission/AddPermissionsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Permission\\\\PermissionsTypes extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Permission/PermissionsTypes.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\RelationType\\\\EditRelationTypeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/RelationType/EditRelationTypeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\AbstractReviewType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/AbstractReviewType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Review\\\\AbstractReviewType\\:\\:addNotes\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/AbstractReviewType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Review\\\\AbstractReviewType\\:\\:addReviewer\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/AbstractReviewType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Review\\\\AbstractReviewType\\:\\:addReviewer\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/AbstractReviewType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\DisplayPendingChangeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$offset of method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type of call to method ArrayAccess\\<key\\-of\\<mixed\\>,value\\-of\\<mixed\\>\\>\\:\\:offsetGet\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'method.unresolvableReturnType',
	'count' => 2,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between null and App\\\\Entity\\\\PendingChange will always evaluate to false\\.$#',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/DisplayPendingChangeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\ReviewDiff\\\\AbstractReviewDiffType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/AbstractReviewDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Review\\\\ReviewDiff\\\\AbstractReviewDiffType\\:\\:getPendingChange\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/AbstractReviewDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewCheckboxDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewRelationDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewSimpleListDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrArray of method Symfony\\\\Component\\\\PropertyAccess\\\\PropertyAccessor\\:\\:getValue\\(\\) expects array\\|object, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewDiff/ReviewTextDiffType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionObjectFooterType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\ReviewSubmissionObjectFooterType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionObjectFooterType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\ReviewSubmissionObjectHeaderType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionObjectHeaderType.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Review\\\\ReviewSubmissionType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Review\\\\ReviewSubmissionType\\:\\:getFormTypeForField\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Review/ReviewSubmissionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\StudyArea\\\\EditStudyAreaType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyArea/EditStudyAreaType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\StudyArea\\\\FieldConfigurationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyArea/FieldConfigurationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\StudyArea\\\\FieldConfigurationType\\:\\:conceptFields\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyArea/FieldConfigurationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\StudyArea\\\\FieldConfigurationType\\:\\:learningOutcomeFields\\(\\) has parameter \\$builder with generic interface Symfony\\\\Component\\\\Form\\\\FormBuilderInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyArea/FieldConfigurationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\StudyArea\\\\TransferOwnerType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyArea/TransferOwnerType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\StudyAreaGroup\\\\StudyAreaGroupType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/StudyAreaGroup/StudyAreaGroupType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Tag\\\\EditTagType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Tag/EditTagType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\ButtonUrlType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/ButtonUrlType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\CkEditorType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/CkEditorType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\EmailListType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/EmailListType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/EmailListType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\HiddenEntityType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/HiddenEntityType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\NewPasswordType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/NewPasswordType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\OrderedCollectionElementType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/OrderedCollectionElementType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\OrderedCollectionType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/OrderedCollectionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\PrintedTextType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/PrintedTextType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\RemoveType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/RemoveType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Type\\\\RemoveType\\:\\:isRemoveClicked\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/RemoveType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\SaveType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/SaveType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Form\\\\Type\\\\SaveType\\:\\:isListClicked\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/SaveType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\Type\\\\SingleSubmitType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/Type/SingleSubmitType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\AddFallbackUserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/AddFallbackUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\AddFallbackUsersType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/AddFallbackUsersType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\ChangePasswordType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/ChangePasswordType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\EditFallbackUserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/EditFallbackUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\GenerateApiTokenType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/GenerateApiTokenType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Form\\\\User\\\\UpdatePasswordType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/User/UpdatePasswordType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedConceptNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedConceptNames.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedLearningOutcomeNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedLearningOutcomeNames.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedNames\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedNames.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Naming\\\\Model\\\\ResolvedNamesInterface\\:\\:resolvePlurals\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/Model/ResolvedNamesInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$studyArea of method App\\\\Naming\\\\NamingService\\:\\:getCached\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Naming/NamingService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\AbbreviationRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AbbreviationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\AnnotationRepository\\:\\:getCountsForUserInStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\AnnotationRepository\\:\\:getForUserAndStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$studyArea of method App\\\\Repository\\\\AnnotationRepository\\:\\:getVisibilityWhere\\(\\) expects App\\\\Entity\\\\StudyArea, App\\\\Entity\\\\StudyArea\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/AnnotationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Repository\\\\ConceptRelationRepository uses generic trait Drenso\\\\Shared\\\\Database\\\\RepositoryTraits\\\\FindIdsTrait but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ConceptRelationRepository\\:\\:getByRelationTypeCount\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ConceptRelationRepository\\:\\:getCountForStudyArea\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRelationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Repository\\\\ConceptRepository uses generic trait Drenso\\\\Shared\\\\Database\\\\RepositoryTraits\\\\FindIdsTrait but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:getCountForStudyArea\\(\\) should return int but returns bool\\|float\\|int\\|string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:loadRelations\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ConceptRepository\\:\\:preLoadData\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ConceptRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ContributorRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContributorRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ContributorRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContributorRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ExternalResourceRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ExternalResourceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\ExternalResourceRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ExternalResourceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findForConcepts\\(\\) has parameter \\$concepts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findUnusedNumberInStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:findUsedConceptIdsForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningOutcomeRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningOutcomeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\Concept\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningPathRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\LearningPathRepository\\:\\:removeElementBasedOnConcept\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LearningPathRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\PageLoadRepository\\:\\:purgeForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PageLoadRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\PendingChangeRepository\\:\\:getMultiple\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/PendingChangeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:flush\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/RelationTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\StudyAreaRepository\\:\\:getOwnerAmount\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/StudyAreaRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\TagRepository\\:\\:findForStudyArea\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TagRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\TagRepository\\:\\:getCountForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TagRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\TrackingEventRepository\\:\\:purgeForStudyArea\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/TrackingEventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\UserGroupRepository\\:\\:removeObsoleteGroups\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserGroupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\UserProtoRepository\\:\\:getForEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserProtoRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getFallbackUsers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getUserForEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Repository\\\\UserRepository\\:\\:getUsersForEmails\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on ReflectionType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Left side of && is always true\\.$#',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Subscriber/RequestStudyAreaSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Request\\\\Wrapper\\\\RequestStudyArea\\:\\:getStudyAreaId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Request/Wrapper/RequestStudyArea.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getId\\(\\) on an unknown class App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getReviewName\\(\\) on an unknown class App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$reviewable of method App\\\\Review\\\\Exception\\\\IncompatibleChangeException\\:\\:__construct\\(\\) has invalid type App\\\\Entity\\\\Traits\\\\ReviewableTrait\\.$#',
	'identifier' => 'parameter.trait',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeException.php',
];
$ignoreErrors[] = [
	'message' => '#^Constructor of class App\\\\Review\\\\Exception\\\\IncompatibleChangeMergeException has an unused parameter \\$merge\\.$#',
	'identifier' => 'constructor.unusedParameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/IncompatibleChangeMergeException.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_intersect expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/OverlappingFieldsChangedException.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_intersect expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Exception/OverlappingFieldsChangedException.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between App\\\\Entity\\\\PendingChange and App\\\\Entity\\\\PendingChange will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Model/PendingChangeObjectInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Model/PendingChangeObjectInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_merge expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/Model/PendingChangeObjectInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getSession\\(\\) on null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:asSimpleType\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:asSimpleType\\(\\) should return string\\|false\\|null but returns float\\|int\\|string\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:createReview\\(\\) has parameter \\$markedChanges with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Review\\\\ReviewService\\:\\:determineChangedFieldsFromSnapshot\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:flush\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 3,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for property App\\\\Review\\\\ReviewService\\:\\:\\$serializer with type JMS\\\\Serializer\\\\SerializerInterface\\|null is not subtype of native type JMS\\\\Serializer\\\\Serializer\\|null\\.$#',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$className of method Doctrine\\\\ORM\\\\EntityManagerInterface\\:\\:getRepository\\(\\) expects class\\-string\\<object\\>, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arrays of function array_merge expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$object of method App\\\\Review\\\\ReviewService\\:\\:isReviewModeEnabledForObject\\(\\) expects App\\\\Entity\\\\Contracts\\\\ReviewableInterface, App\\\\Entity\\\\Contracts\\\\ReviewableInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$originalSnapshot of method App\\\\Review\\\\ReviewService\\:\\:determineChangedFieldsFromSnapshot\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between App\\\\Entity\\\\PendingChange and \'30_remove\' will always evaluate to false\\.$#',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between null and string will always evaluate to false\\.$#',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type T in call to method Doctrine\\\\ORM\\\\EntityManagerInterface\\:\\:getRepository\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/src/Review/ReviewService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$parameters with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generate\\(\\) has parameter \\$referenceType with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:generateBrowserUrl\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:getContext\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:getRouteCollection\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:match\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:match\\(\\) has parameter \\$pathinfo with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Router\\\\LtbRouter\\:\\:setContext\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Router/LtbRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'path\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Http/Authentication/AuthenticationSuccessHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Security\\\\Http\\\\Authentication\\\\AuthenticationSuccessHandler\\:\\:__construct\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Http/Authentication/AuthenticationSuccessHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDisplayName\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmail\\(\\) on App\\\\Entity\\\\UserGroupEmail\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserIdentifier\\(\\) on App\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Security\\\\UserPermissions\\:\\:addPermissionFromGroup\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/UserPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Security\\\\Voters\\\\MenuVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Voters/MenuVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Security\\\\Voters\\\\StudyAreaVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/Voters/StudyAreaVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Serializer\\\\Handler\\\\LearningPathVisualisationResultHandler\\:\\:getSubscribedEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Serializer/Handler/LearningPathVisualisationResultHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always false\\.$#',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/Serializer/Handler/LearningPathVisualisationResultHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot assign offset \'sFirst\'\\|\'sLast\'\\|\'sNext\'\\|\'sPrevious\'\\|\'sSortAscending\'\\|\'sSortDescending\' to array\\<string, string\\>\\|string\\|null\\.$#',
	'identifier' => 'offsetAssign.dimType',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:dataTable\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:dataTable\\(\\) has parameter \\$tableId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:getDefaultDataTableOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\DataTableExtension\\:\\:getDutchDataTableTranslation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\!\\=\\= between \'\\: activeer om kolom…\'\\|\'Eerste\'\\|\'Laatste\'\\|\'Volgende\'\\|\'Vorige\' and null will always evaluate to true\\.$#',
	'identifier' => 'notIdentical.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DataTableExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) has parameter \\$search with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) has parameter \\$text with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\HighlightExtension\\:\\:hilightFilter\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HighlightExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\LtbRouterExtension\\:\\:browserPath\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LtbRouterExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\LtbRouterExtension\\:\\:browserPath\\(\\) has parameter \\$parameters with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LtbRouterExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Twig\\\\TranslationStringExtension\\:\\:trString\\(\\) has parameter \\$text with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/TranslationStringExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\Model\\\\Url\\:\\:getUrlParts\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/Url.php',
];
$ignoreErrors[] = [
	'message' => '#^Strict comparison using \\=\\=\\= between string and null will always evaluate to false\\.$#',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$id on left side of \\?\\? always exists and is not nullable\\.$#',
	'identifier' => 'nullCoalesce.variable',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$path on left side of \\?\\? always exists and is not nullable\\.$#',
	'identifier' => 'nullCoalesce.variable',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/Model/UrlContext.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:cacheUrl\\(\\) has parameter \\$expiry with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:checkAllUrls\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:checkStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:findBadUrls\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlChecker\\:\\:getUrlsForStudyArea\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$value of function curl_setopt expects non\\-empty\\-string, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\UrlUtils\\\\UrlScanner\\:\\:_scanText\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/UrlUtils/UrlScanner.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$id of class App\\\\UrlUtils\\\\Model\\\\UrlContext constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 12,
	'path' => __DIR__ . '/src/UrlUtils/UrlScanner.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Validator/Constraint/ConceptRelationValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Validator\\\\Constraint\\\\ConceptRelationValidator\\:\\:\\$violations type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/Constraint/ConceptRelationValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:scanTextProvider\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:testScanText\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\UrlUtils\\\\UrlScannerTest\\:\\:testScanText\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/UrlUtils/UrlScannerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function method_exists\\(\\) with \'Symfony\\\\\\\\Component\\\\\\\\Dotenv\\\\\\\\Dotenv\' and \'bootEnv\' will always evaluate to true\\.$#',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/tests/phpunit/bootstrap.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
