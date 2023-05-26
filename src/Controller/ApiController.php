<?php

namespace App\Controller;

use App\Entity\Error;
use App\Entity\Answer;
use App\Repository\ErrorRepository;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/errors", methods={"GET"})
     */
    public function getErrors(ErrorRepository $errorRepository): JsonResponse
    {
        $errors = $errorRepository->findAll();
        $data = [];

        foreach ($errors as $error) {
            $data[] = [
                'id' => $error->getId(),
                'title' => $error->getTitle(),
                'description' => $error->getDescription(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/errors", methods={"POST"})
     */
    public function createError(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $error = new Error();
        $error->setTitle($requestData['title']);
        $error->setDescription($requestData['description']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($error);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Error created successfully'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/answers", methods={"POST"})
     */
    public function createAnswer(Request $request, ErrorRepository $errorRepository): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $errorId = $requestData['errorId'];
        $error = $errorRepository->find($errorId);

        if (!$error) {
            return new JsonResponse(['error' => 'Error not found'], Response::HTTP_NOT_FOUND);
        }

        $answer = new Answer();
        $answer->setReponse($requestData['reponse']);
        $answer->setError($error);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($answer);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Answer created successfully'], Response::HTTP_CREATED);
    }
}
