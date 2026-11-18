<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Data\Export\ExportService;
use App\Data\Import\ImportService;
use App\Dto\ExportTypeDto;
use App\Dto\ImportTypeDto;
use App\DuplicationUtils\StudyAreaDuplicator;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Excel\StudyAreaStatusBuilder;
use App\Exception\DataImportException;
use App\Form\Data\DownloadType;
use App\Form\Data\DuplicateType;
use App\Form\Data\ImportType;
use App\Naming\NamingService;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Repository\RelationTypeRepository;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Router\LtbRouter;
use App\Security\Voters\StudyAreaVoter;
use App\Skills\SkillAccessor;
use App\UrlUtils\UrlScanner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Drenso\Shared\Exception\NullGuard\MustNotBeNullException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function file_get_contents;
use function is_numeric;
use function is_string;
use function mb_convert_encoding;
use function ob_get_clean;
use function ob_start;
use function set_time_limit;
use function sprintf;
use function strlen;

#[Route('/{_studyArea<\d+>}/data')]
class DataController extends AbstractController
{
  /** Export for concept browser and search. Search part currently isn't used, but it kept for now. */
  #[Route('/export', name: 'app_data_export', options: ['expose' => true], defaults: ['export' => true])]
  #[Route('/search', name: 'app_data_search', options: ['expose' => true], defaults: ['export' => false])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function export(
    bool $export,
    RelationTypeRepository $relationTypeRepo,
    ConceptRepository $conceptRepo,
    SerializerInterface $serializer,
    RequestStudyArea $requestStudyArea,
  ): Response {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $relationTypeRepo->findBy(['studyArea' => $requestStudyArea->getStudyArea()]);

    // Retrieve the concepts
    $concepts = $conceptRepo->findForStudyAreaOrderedByName($requestStudyArea->getStudyArea());

    // Return as JSON
    $groups = ['Default'];
    if ($export) {
      $groups[] = 'relations';
    }
    $json = $serializer->serialize($concepts, 'json', SerializationContext::create()->setGroups($groups));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /** @throws Exception */
  #[Route('/excel')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function excelStatus(RequestStudyArea $requestStudyArea, StudyAreaStatusBuilder $builder): Response
  {
    return $builder->build($requestStudyArea->getStudyArea());
  }

  /** @throws NonUniqueResultException */
  #[Route('/upload')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_default_dashboard', subject: 'requestStudyArea')]
  public function upload(
    Request $request,
    RequestStudyArea $requestStudyArea,
    SerializerInterface $serializer,
    TranslatorInterface $translator,
    EntityManagerInterface $em,
    ImportService $importService,
    NamingService $namingService,
  ): Response {
    $studyArea = $requestStudyArea->getStudyArea();

    if ($studyArea->isReviewModeEnabled()) {
      // We do not support upload in areas where the review mode is enabled
      $this->addFlash('warning', $translator->trans('data.import.upload-json-disabled-review-mode'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    $form = $this->createForm(ImportType::class, $data = new ImportTypeDto(), [
      'study_area' => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        // Handle new upload
        $json = $data->json;

        // Check file format
        if (!$json instanceof UploadedFile) {
          throw new DataImportException('File upload failed');
        }

        // Expand default time limit for large imports
        set_time_limit(600); // 10 minutes

        try {
          $jsonFile = file_get_contents($json->getPathname());
          if ($jsonFile === false) {
            throw new DataImportException('Could not open file');
          }
          $contents = mb_convert_encoding($jsonFile, 'UTF-8', 'UTF-8');

          // Extra json check as we now allow text/html uploads
          if (!$this->couldBeJson($contents)) {
            throw new DataImportException('Invalid JSON contents detected');
          }

          $jsonData = $serializer->deserialize($contents, 'array', 'json');
        } catch (\Exception $e) {
          throw new DataImportException('Deserialization error, did you upload a valid JSON file?', 0, $e);
        }

        $importService->import($studyArea, $data->type ?? throw new MustNotBeNullException(), $jsonData);

        // Save the data
        $em->flush();
        $namingService->clearCache();
        $this->addFlash('success', $translator->trans('data.import.json-uploaded'));
        $this->redirectToRoute('app_data_upload');
      } catch (DataImportException $e) {
        $this->addFlash('error', $translator->trans('data.import.json-incorrect', ['%message%' => $e->getMessage()]));
      }
    }

    return $this->render('data/import.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/download')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function download(Request $request, RequestStudyArea $requestStudyArea, ExportService $exportService): Response
  {
    $form = $this->createForm(DownloadType::class, $data = new ExportTypeDto(), [
      'validation_groups' => [ExportTypeDto::GROUP_DOWNLOAD],
      'study_area'        => $requestStudyArea->getStudyArea(),
    ]);
    $form->handleRequest($request);

    $studyArea = $requestStudyArea->getStudyArea();
    if ($form->isSubmitted() && $form->isValid()) {
      return $exportService->export($studyArea, $data->type);
    }

    return $this->render('data/export.html.twig', [
      'studyArea' => $studyArea,
      'form'      => $form,
    ]);
  }

  #[Route('/export/url', name: 'app_data_export_url', options: ['expose' => true])]
  #[IsGranted(User::ROLE_SUPER_ADMIN, subject: 'requestStudyArea')]
  public function exportToUrl(
    Request $request,
    RequestStudyArea $requestStudyArea,
    TranslatorInterface $translator,
    ExportService $exportService,
    HttpClientInterface $httpClient,
  ): Response {
    $studyArea = $requestStudyArea->getStudyArea();

    if (!$studyArea->isUrlExportEnabled()) {
      $this->addFlash('error', sprintf('%s', $translator->trans('data.export-url.invalid-export-setting')));

      return $this->redirectToRoute('app_default_dashboard');
    }

    $data = new ExportTypeDto(exportUrl: $studyArea->getExportUrl());
    $form = $this->createForm(DownloadType::class, $data, [
      'form_target'        => '_self',
      'url_export_enabled' => true,
      'validation_groups'  => [ExportTypeDto::GROUP_EXPORT],
      'study_area'         => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $exportResponse = $exportService->export($studyArea, $data->type);

      if ($exportResponse instanceof StreamedResponse) {
        ob_start();
        $exportResponse->sendContent();
        $content = ob_get_clean();
      } else {
        $content = $exportResponse->getContent();
      }

      // Ensured by form validation
      $exportUrl = $data->exportUrl ?? throw new MustNotBeNullException();

      $response  = $httpClient->request(
        $data->httpMethod,
        sprintf('%s/%s-%s.json', $exportUrl, $studyArea->getName(), $studyArea->getId()),
        [
          'headers' => [
            'Content-Type' => $exportResponse->headers->get('Content-Type', 'application/json'),
          ],
          'body' => $content,
        ],
      );

      if ($response->getStatusCode() == Response::HTTP_OK) {
        $this->addFlash(
          'success',
          sprintf('%s: "%s"', $translator->trans('data.export-url.success'), $exportUrl),
        );

        return $this->redirectToRoute('app_data_export_url');
      }

      $this->addFlash(
        'error',
        sprintf('%s: "%s".', $translator->trans('data.export-url.error'), $exportUrl),
      );
    }

    return $this->render('data/export_url.html.twig', [
      'studyArea' => $studyArea,
      'form'      => $form,
    ]);
  }

  /** @throws \Exception */
  #[Route('/duplicate')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function duplicate(
    Request $request,
    #[CurrentUser] User $user,
    RequestStudyArea $requestStudyArea,
    TranslatorInterface $trans,
    EntityManagerInterface $em,
    UrlScanner $urlScanner,
    LtbRouter $router,
    SkillAccessor $skillAccessor,
    AbbreviationRepository $abbreviationRepo,
    ConceptRelationRepository $conceptRelationRepo,
    ContributorRepository $contributorRepository,
    ExternalResourceRepository $externalResourceRepo,
    LearningOutcomeRepository $learningOutcomeRepo,
    LearningPathRepository $learningPathRepo,
    TagRepository $tagRepository,
  ): Response {
    // Create form to select the concepts for this study area
    $studyAreaToDuplicate = $requestStudyArea->getStudyArea();
    $newStudyArea         = new StudyArea()
      ->setOwner($user)
      ->setAccessType(StudyArea::ACCESS_PRIVATE)
      ->setDescription($studyAreaToDuplicate->getDescription())
      ->setPrintHeader($studyAreaToDuplicate->getPrintHeader())
      ->setPrintIntroduction($studyAreaToDuplicate->getPrintIntroduction());

    $form = $this->createForm(DuplicateType::class, [
      DuplicateType::NEW_STUDY_AREA => $newStudyArea,
    ], [
      'current_study_area' => $studyAreaToDuplicate,
      'new_study_area'     => $newStudyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data      = $form->getData();
      $selectAll = $data[DuplicateType::SELECT_ALL];
      if ($selectAll) {
        $concepts = $studyAreaToDuplicate->getConcepts();
      } else {
        $concepts = $data[DuplicateType::CONCEPTS];
      }

      if ($data[DuplicateType::CHOICE] === DuplicateType::CHOICE_EXISTING) {
        $targetStudyArea = $data[DuplicateType::EXISTING_STUDY_AREA];
      } else {
        $targetStudyArea = $newStudyArea;
      }

      // Duplicate the data
      $duplicator = new StudyAreaDuplicator(
        $this->getParameter('kernel.project_dir'), $em, $urlScanner, $router, $skillAccessor,
        $abbreviationRepo, $conceptRelationRepo, $contributorRepository, $externalResourceRepo, $learningOutcomeRepo,
        $learningPathRepo, $tagRepository, $studyAreaToDuplicate, $targetStudyArea, $concepts->toArray());
      $duplicator->duplicate();

      $this->addFlash('success', $trans->trans('data.concepts-duplicated'));

      // Load reloading page in order to switch to the duplicated study area
      return $this->render('reloading_fullscreen.html.twig', [
        'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $targetStudyArea->getId()]),
      ]);
    }

    return $this->render('data/duplicate.html.twig', [
      'form'      => $form,
      'studyArea' => $studyAreaToDuplicate,
    ]);
  }

  /** Based on https://stackoverflow.com/a/45241792/1439286. */
  private function couldBeJson(mixed $value): bool
  {
    // Numeric strings are always valid JSON.
    if (is_numeric($value)) {
      return true;
    }

    // A non-string value can never be a JSON string.
    if (!is_string($value)) {
      return false;
    }

    // Any non-numeric JSON string must be longer than 2 characters.
    if (strlen($value) < 2) {
      return false;
    }

    // "null" is valid JSON string.
    if ('null' === $value) {
      return true;
    }

    // "true" and "false" are valid JSON strings.
    if ('true' === $value) {
      return true;
    }
    if ('false' === $value) {
      return false;
    }

    // Any other JSON string has to be wrapped in {}, [] or "".
    if ('{' != $value[0] && '[' != $value[0] && '"' != $value[0]) {
      return false;
    }

    // Verify that the trailing character matches the first character.
    $last_char = $value[strlen($value) - 1];
    if ('{' == $value[0] && '}' != $last_char) {
      return false;
    }
    if ('[' == $value[0] && ']' != $last_char) {
      return false;
    }
    if ('"' == $value[0] && '"' != $last_char) {
      return false;
    }

    return true;
  }
}
