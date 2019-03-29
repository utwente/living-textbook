<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Repository\ConceptRepository;
use App\Repository\LearningPathRepository;
use EasyRdf_Exception;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use EasyRdf_Serialiser_JsonLd;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RdfProvider implements ProviderInterface
{
  /** @var ConceptRepository */
  private $conceptRepository;

  /** @var LearningPathRepository */
  private $learningPathRepository;

  /** @var RouterInterface */
  private $router;

  /** @var SerializerInterface */
  private $serializer;

  public function __construct(ConceptRepository $conceptRepository, LearningPathRepository $learningPathRepository, RouterInterface $router, SerializerInterface $serializer)
  {
    $this->conceptRepository      = $conceptRepository;
    $this->learningPathRepository = $learningPathRepository;
    $this->router                 = $router;
    $this->serializer             = $serializer;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return JsonResponse
   * @throws EasyRdf_Exception
   */
  public function exportStudyArea(StudyArea $studyArea): JsonResponse
  {
    $graph = new EasyRdf_Graph();
    $this->addStudyAreaResource($studyArea, $graph);
    foreach ($studyArea->getRelationTypes() as $relationType) {
      $this->addRelationTypeResource($relationType, $graph);
    }

    $concepts = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);

    foreach ($concepts as $concept) {
      $this->addConceptResource($concept, $graph);
    }

    return $this->exportGraph($graph);
  }

  public function getName(): string
  {
    return 'rdf';
  }

  public function getPreview(): string
  {
    return <<<'EOT'
[
  {
    "@id": "<concept-url>",
    "@type": [
      "rdfs:Class",
      "skos:Concept"
    ],
    "<relation-url>": {
      "@id": "<related-concept-url>"
    },
    "<learning-outcome-url>": {
      "@id": "<learning-outcome-resource-url>"
    },
    "<prior-knowledge-url>": {
      "@id": "<prior-knowledge-concept-url>"
    },
    "rdfs:label": [
      {
        "@language": "en",
        "@value": "<concept-name>"
      },
      {
        "@language": "en",
        "@value": "<concept-synonyms>"
      }
    ],
    "skos:altLabel": {
      "@language": "en",
      "@value": "<concept-synonyms>"
    },
    "skos:inScheme": {
      "@id": "<study-area-url>"
    },
    "skos:prefLabel": {
      "@language": "en",
      "@value": "<concept-name>"
    }
  },
  {
    "@id": "<relation-url>",
    "@type": "rdf:Property",
    "dcterms:creator": "<relation-owner>",
    "rdfs:comment": {
      "@language": "en",
      "@value": "<relation-description>"
    },
    "rdfs:domain": {
      "@id": "skos:Concept"
    },
    "rdfs:label": {
      "@language": "en",
      "@value": "<relation-name>"
    },
    "rdfs:range": {
      "@id": "skos:Concept"
    },
    "skos:inScheme": {
      "@id": "<study-area-url>"
    }
  },
  {
    "@id": "<study-area-url>",
    "@type": [
      "skos:ConceptScheme",
      "https://ltb.itc.utwente.nl/resource/Study_Area"
    ],
    "dcterms:creator": "<study-area-owner>",
    "rdfs:label": {
      "@language": "en",
      "@value": "<study-area-name>"
    }
  }
]
EOT;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Response
   * @throws EasyRdf_Exception
   */
  public function export(StudyArea $studyArea): Response
  {
    $response = $this->exportStudyArea($studyArea);
    ExportService::contentDisposition($response, sprintf('%s_rdf_export.json', $studyArea->getName()));

    return $response;
  }

  /**
   * @param RelationType  $relationType
   * @param EasyRdf_Graph $graph
   */
  public function addRelationTypeResource(RelationType $relationType, EasyRdf_Graph $graph): void
  {
    $relationTypeResource = $graph->resource('https://ltb.itc.utwente.nl/relationType/' . $relationType->getCamelizedName());
    $relationTypeResource->setType('rdf:Property');
    $relationTypeResource->addLiteral('dcterms:creator', $relationType->getStudyArea()->getOwner()->getFullName());
    $relationTypeResource->addLiteral('rdfs:comment', $relationType->getDescription(), 'en');
    $relationTypeResource->add('rdfs:domain', ['type' => 'uri', 'value' => EasyRdf_Namespace::expand('skos:Concept')]);
    $relationTypeResource->addLiteral('rdfs:label', $relationType->getName(), 'en');
    $relationTypeResource->add('rdfs:range', ['type' => 'uri', 'value' => EasyRdf_Namespace::expand('skos:Concept')]);
    $relationTypeResource->add('skos:inScheme', ['type' => 'uri', 'value' => $this->generateStudyAreaResourceUrl()]);
  }

  /**
   * @param StudyArea     $studyArea
   * @param EasyRdf_Graph $graph
   */
  public function addStudyAreaResource(StudyArea $studyArea, EasyRdf_Graph $graph): void
  {
    $studyAreaResource = $graph->resource($this->generateStudyAreaResourceUrl());
    $studyAreaResource->addType('skos:ConceptScheme');
    $studyAreaResource->addType('https://ltb.itc.utwente.nl/resource/studyarea');
    $studyAreaResource->addLiteral('dcterms:creator', $studyArea->getOwner()->getFullName());
    $studyAreaResource->addLiteral('rdfs:label', $studyArea->getName(), 'en');
  }

  /**
   * @param Concept       $concept
   * @param EasyRdf_Graph $graph
   */
  public function addConceptResource(Concept $concept, EasyRdf_Graph $graph): void
  {
    $conceptResource = $graph->resource($this->generateConceptResourceUrl($concept));
    $conceptResource->addType('rdfs:Class');
    $conceptResource->addType('skos:Concept');
    foreach ($concept->getOutgoingRelations() as $outgoingRelation) {
      $conceptResource->add('https://ltb.itc.utwente.nl/resource/' . $outgoingRelation->getRelationType()->getCamelizedName(), ['type' => 'uri', 'value' => $this->generateConceptResourceUrl($outgoingRelation->getTarget())]);
    }
    foreach ($concept->getLearningOutcomes() as $learningOutcome) {
      $conceptResource->add('https://ltb.itc.utwente.nl/resource/learningoutcome', ['type' => 'uri', 'value' => $this->generateLearningOutcomeResourceUrl($learningOutcome)]);
    }
    foreach ($this->learningPathRepository->findForConcept($concept) as $learningPath) {
      $conceptResource->add('https://ltb.itc.utwente.nl/resource/learningpath', ['type' => 'uri', 'value' => $this->generateLearningPathResourceUrl($learningPath)]);
    }
    foreach ($concept->getPriorKnowledge() as $priorKnowledge) {
      $conceptResource->add('https://ltb.itc.utwente.nl/resource/priorknowledge', ['type' => 'uri', 'value' => $this->generateConceptResourceUrl($priorKnowledge)]);
    }
    foreach ([$concept->getName(), $concept->getSynonyms()] as $label) {
      if ($label !== '') $conceptResource->addLiteral('rdfs:label', $label, 'en');
    }
    if ($concept->getSynonyms() !== '') $conceptResource->addLiteral('skos:altLabel', $concept->getSynonyms(), 'en');
    $conceptResource->add('skos:inScheme', ['type' => 'uri', 'value' => $this->generateStudyAreaResourceUrl()]);
    $conceptResource->addLiteral('skos:prefLabel', $concept->getName(), 'en');
  }

  /**
   * @param LearningOutcome $learningOutcome
   * @param EasyRdf_Graph   $graph
   */
  public function addLearningOutcomeResource(LearningOutcome $learningOutcome, EasyRdf_Graph $graph): void
  {
    $learningOutcomeResource = $graph->resource($this->generateLearningOutcomeResourceUrl($learningOutcome));
    $learningOutcomeResource->addType('https://ltb.itc.utwente.nl/resource/learningoutcome');
  }

  /**
   * @param LearningPath  $learningPath
   * @param EasyRdf_Graph $graph
   */
  public function addLearningPathResource(LearningPath $learningPath, EasyRdf_Graph $graph): void
  {
    $learningPathResource = $graph->resource($this->generateLearningPathResourceUrl($learningPath));
    $learningPathResource->addType('https://ltb.itc.utwente.nl/resource/learningpath');
  }

  /**
   * @param EasyRdf_Graph $graph
   *
   * @return JsonResponse
   * @throws EasyRdf_Exception
   */
  public function exportGraph(EasyRdf_Graph $graph): JsonResponse
  {
    $jsonLd = (new EasyRdf_Serialiser_JsonLd())->serialise($graph, 'jsonld');
    // Pretty print JSON
    $jsonLd = $this->serializer->deserialize($jsonLd, 'array', 'json');
    $jsonLd = $this->serializer->serialize($jsonLd, 'json');

    return new JsonResponse($jsonLd, Response::HTTP_OK, [], true);
  }

  /**
   * @return string
   */
  public function generateStudyAreaResourceUrl(): string
  {
    return $this->router->generate('app_resource_studyarea', [], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  /**
   * @param Concept $concept
   *
   * @return string
   */
  public function generateConceptResourceUrl(Concept $concept): string
  {
    return $this->router->generate('app_resource_concept', ['concept' => $concept->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  /**
   * @param LearningOutcome $learningOutcome
   *
   * @return string
   */
  public function generateLearningOutcomeResourceUrl(LearningOutcome $learningOutcome): string
  {
    return $this->router->generate('app_resource_learningoutcome', ['learningOutcome' => $learningOutcome->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  /**
   * @param LearningPath $learningPath
   *
   * @return string
   */
  public function generateLearningPathResourceUrl(LearningPath $learningPath): string
  {
    return $this->router->generate('app_resource_learningpath', ['learningPath' => $learningPath->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }
}
