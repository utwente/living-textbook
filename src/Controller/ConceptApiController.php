<?php

namespace App\Controller;

use App\Entity\Concept;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ConceptRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Review\ReviewService;
use Doctrine\Common\Persistence\ManagerRegistry;

class ConceptApiController extends AbstractController
{
    /**
     * @author Robert
     */

    public function __invoke(
        Request $request,
        ConceptRepository $conceptRepository,
        ReviewService $reviewService,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        SerializerInterface $serializer
    ) {
        $contents = $request->getContent();
        if (!empty($contents)) {
            $concepts = $serializer->deserialize($contents, 'array', 'json');
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
                $studyArea = $concept->getStudyArea();

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
}
