<?php

namespace App\Controller;

use App\Repository\ResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
class ResponseController extends AbstractController
{
    private $responseRepository;
    private $serializer;

    private $dataManager;
    public function __construct(ResponseRepository $responseRepositoryParam, EntityManagerInterface $managerParam, SerializerInterface $serializerParam){
        $this->responseRepository = $responseRepositoryParam;
        $this->serializer = $serializerParam;
        $this->dataManager = $managerParam;
    }

    #[Route("api/response/{id}", name: "deleteResponse", methods: ['DELETE'])]
    public function deleteResponse(int $id): JsonResponse {
        $responseToDelete = $this->responseRepository->find($id);

        if(!$responseToDelete){
            return $this->json(['error' => 'Response not found'], status: 404);
        }

        $this->dataManager->remove($responseToDelete);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response remove successfully', 'idResponse' => $id]);
    }

}