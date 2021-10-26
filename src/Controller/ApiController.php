<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Annotation\DenyOnFrozenStudyArea;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ConceptRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Review\ReviewService;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;


/**
 * Class ApiController
 *
 * @author Robert
 * @Route("/api/{_studyArea}", requirements={"_studyArea"="\d+"})
 */

class ApiController extends AbstractController
{
    /**
     * @Route("/concepts/updatemany", name="api_many_concepts_update", methods={"PATCH"}, options={"expose"=true}, defaults={"export"=true})
     * @Template()
     * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
     * @DenyOnFrozenStudyArea(route="api_many_concepts_update", subject="requestStudyArea")
     *
     * @param Request             $request
     * @param ConceptRepository   $conceptRepository
     * @param ReviewService       $reviewService
     * @param ValidatorInterface  $validator
     * @param ManagerRegistry     $registry
     * @param RequestStudyArea    $requestStudyArea
     *
     * @return JsonResponse
     */
    public function updateMany(
        Request $request,
        ConceptRepository $conceptRepository,
        ReviewService $reviewService,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        RequestStudyArea    $requestStudyArea
    ) {
        $contents = $request->getContent();

        if (!empty($contents)) {
            $concepts = json_decode($contents, true);
            $success = true;

            if (sizeof($concepts) == 0) {
                return new JsonResponse(
                    json_encode(array('success' => false)),
                    Response::HTTP_NO_CONTENT,
                    [],
                    true
                );
            }
            $em = $registry->getManagerForClass(Concept::class);
            foreach ($concepts as $jsonConcept) {
                $concept = $conceptRepository->findOneBy(["id" => $jsonConcept['id']]);
                if (!$concept) {
                    $success = false;
                    continue;
                }
                $studyArea = $requestStudyArea->getStudyArea();

                if ($reviewService->canObjectBeEdited($studyArea, $concept)) {
                    $concept->setModelCfg($jsonConcept['modelCfg']);
                    if ($validator->validate($concept)->count() > 0) {
                        $success = false;
                    } else {
                        $em->persist($concept);
                    }
                }
            }
            if ($success) {
                $em->flush();
                $result = array('success' => $success);
                return new JsonResponse(json_encode($result), Response::HTTP_OK, [], true);
            } else {
                $em->flush();
                $result = array('success' => $success);
                return new JsonResponse(json_encode($result), Response::HTTP_NOT_MODIFIED, [], true);
            }
        }

        return new JsonResponse($contents, Response::HTTP_NO_CONTENT, [], true);
    }

    /**
     * @Route("/tags", name="api_get_tags", methods={"GET"}, options={"expose"=true}, defaults={"export"=true})
     * @Template()
     * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
     *
     * @param TagRepository   $tagRepository
     * @param RequestStudyArea    $requestStudyArea
     * @param SerializerInterface $serializer,
     * @return JsonResponse
     */
    public function listTags(TagRepository $tagRepository, RequestStudyArea $requestStudyArea, SerializerInterface $serializer)
    {
        $studyArea = $requestStudyArea->getStudyArea();
        $tags = $tagRepository->findForStudyArea($studyArea);

        // Return as JSON
        $groups = ["Default"];
        $groups[] = "tags";
        $json = $serializer->serialize($tags, 'json', SerializationContext::create()->setGroups($groups));

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
