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
use EasyRdf\Exception;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use EasyRdf\Serialiser\JsonLd;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RdfProvider implements ProviderInterface
{
  private ConceptRepository $conceptRepository;

  private LearningPathRepository $learningPathRepository;

  private RouterInterface $router;

  private SerializerInterface $serializer;

  public function __construct(ConceptRepository $conceptRepository, LearningPathRepository $learningPathRepository, RouterInterface $router, SerializerInterface $serializer)
  {
    $this->conceptRepository      = $conceptRepository;
    $this->learningPathRepository = $learningPathRepository;
    $this->router                 = $router;
    $this->serializer             = $serializer;
  }

  /** @throws Exception */
  public function exportStudyArea(StudyArea $studyArea): JsonResponse
  {
    $graph = new Graph();
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

  #[Override]
  public function getName(): string
  {
    return 'rdf';
  }

  #[Override]
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

  /** @throws Exception */
  #[Override]
  public function export(StudyArea $studyArea): Response
  {
    $response = $this->exportStudyArea($studyArea);
    ExportService::contentDisposition($response, sprintf('%s_rdf_export.json', $studyArea->getName()));

    return $response;
  }

  public function addRelationTypeResource(RelationType $relationType, Graph $graph): void
  {
    $relationTypeResource = $graph->resource('https://ltb.itc.utwente.nl/relationType/' . $relationType->getCamelizedName());
    $relationTypeResource->setType('rdf:Property');
    $relationTypeResource->addLiteral('dcterms:creator', $relationType->getStudyArea()->getOwner()->getFullName());
    $relationTypeResource->addLiteral('rdfs:comment', $relationType->getDescription(), 'en');
    $relationTypeResource->add('rdfs:domain', ['type' => 'uri', 'value' => RdfNamespace::expand('skos:Concept')]);
    $relationTypeResource->addLiteral('rdfs:label', $relationType->getName(), 'en');
    $relationTypeResource->add('rdfs:range', ['type' => 'uri', 'value' => RdfNamespace::expand('skos:Concept')]);
    $relationTypeResource->add('skos:inScheme', ['type' => 'uri', 'value' => $this->generateStudyAreaResourceUrl()]);
  }

  public function addStudyAreaResource(StudyArea $studyArea, Graph $graph): void
  {
    $studyAreaResource = $graph->resource($this->generateStudyAreaResourceUrl());
    $studyAreaResource->addType('skos:ConceptScheme');
    $studyAreaResource->addType('https://ltb.itc.utwente.nl/resource/studyarea');
    $studyAreaResource->addLiteral('dcterms:creator', $studyArea->getOwner()->getFullName());
    $studyAreaResource->addLiteral('rdfs:label', $studyArea->getName(), 'en');
  }

  public function addConceptResource(Concept $concept, Graph $graph): void
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
      if ($label !== '') {
        $conceptResource->addLiteral('rdfs:label', $label, 'en');
      }
    }
    if ($concept->getSynonyms() !== '') {
      $conceptResource->addLiteral('skos:altLabel', $concept->getSynonyms(), 'en');
    }
    $conceptResource->add('skos:inScheme', ['type' => 'uri', 'value' => $this->generateStudyAreaResourceUrl()]);
    $conceptResource->addLiteral('skos:prefLabel', $concept->getName(), 'en');
  }

  public function addLearningOutcomeResource(LearningOutcome $learningOutcome, Graph $graph): void
  {
    $learningOutcomeResource = $graph->resource($this->generateLearningOutcomeResourceUrl($learningOutcome));
    $learningOutcomeResource->addType('https://ltb.itc.utwente.nl/resource/learningoutcome');
  }

  public function addLearningPathResource(LearningPath $learningPath, Graph $graph): void
  {
    $learningPathResource = $graph->resource($this->generateLearningPathResourceUrl($learningPath));
    $learningPathResource->addType('https://ltb.itc.utwente.nl/resource/learningpath');
  }

  /** @throws Exception */
  public function exportGraph(Graph $graph): JsonResponse
  {
    $jsonLd = new JsonLd()->serialise($graph, 'jsonld');
    // Pretty print JSON
    $jsonLd = $this->serializer->deserialize($jsonLd, 'array', 'json');
    $jsonLd = $this->serializer->serialize($jsonLd, 'json');

    return new JsonResponse($jsonLd, Response::HTTP_OK, [], true);
  }

  public function generateStudyAreaResourceUrl(): string
  {
    return $this->router->generate('app_resource_studyarea', [], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function generateConceptResourceUrl(Concept $concept): string
  {
    return $this->router->generate('app_resource_concept', ['concept' => $concept->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function generateLearningOutcomeResourceUrl(LearningOutcome $learningOutcome): string
  {
    return $this->router->generate('app_resource_learningoutcome', ['learningOutcome' => $learningOutcome->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function generateLearningPathResourceUrl(LearningPath $learningPath): string
  {
    return $this->router->generate('app_resource_learningpath', ['learningPath' => $learningPath->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
  }
}
