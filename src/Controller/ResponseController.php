<?php

namespace App\Controller;

use App\Entity\Response;
use App\Entity\UserAccount;
use App\Repository\ResponseRepository;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

class ResponseController extends AbstractController
{
    private $responseRepository;
    private $serializer;

    private $tweetRepository;

    private $dataManager;
    public function __construct(ResponseRepository $responseRepositoryParam, EntityManagerInterface $managerParam, SerializerInterface $serializerParam, TweetRepository $tweetRepository){
        $this->responseRepository = $responseRepositoryParam;
        $this->serializer = $serializerParam;
        $this->dataManager = $managerParam;
        $this->tweetRepository = $tweetRepository;
    }

    #[ROUTE("api/response", name: "createResponse", methods: ["POST"])]
    #[OA\Tag(name:"Response")]
    public function createResponse(Request $request, TokenInterface $token): JsonResponse{
        $data = json_decode($request->getContent(), true);

        $user = $token->getUser();

        if (!($user instanceof UserAccount)) {
            $user = UserAccount::convertFrom($user);
        }

        $responseToSave = new Response();

        $responseToSave->setContent($data["content"]);
        $responseToSave->setNumberLikes(0);
        $responseToSave->setUserAccount($user);

        $tweetToSave = $this->tweetRepository->find($data["idTweet"]);

        if(!$tweetToSave){
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $responseToSave->setTweet($tweetToSave);

        $this->dataManager->persist($responseToSave);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response created successfully', 'idTweet' => $responseToSave->getId()]);

    }

    #[Route("api/response/{id}", name: "deleteResponse", methods: ['DELETE'])]
    #[OA\Tag(name:"Response")]
    public function deleteResponse(int $id, TokenInterface $token): JsonResponse {

        $responseToDelete = $this->responseRepository->find($id);

        $user = $token->getUser();

        if(!($user instanceof UserAccount)){
            $user = UserAccount::convertFrom($user);
        }

        if(!$responseToDelete){
            return $this->json(['error' => 'response not found'], 403);
        }

        if($user->getId() === $responseToDelete->getUserAccount()->getId() || in_array("ADMIN", $user->getRoles())){
            $this->dataManager->remove($responseToDelete);
            $this->dataManager->flush();

            return $this->json(['message' => "Response remove successfully", 'idResponse' => $id]);
        }

        return $this->json(['error' => 'Access Denied'], 401);#Non autorisé
    }

    #[Route("api/response/unincrementLikes/{id}", name: "unincrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name:"Response")]
    public function unincrementLikes(int $id): JsonResponse
    {

        $responseToPatch = $this->responseRepository->find($id);

        if (!$responseToPatch) {
            return $this->json(['error' => 'Response not found'], 404);
        }

        $increment = $responseToPatch->getNumberLikes() - 1;

        if ($increment < 0) {
            return $this->json(['error' => 'Response has already numberLikes of 0'], 406); #Not acceptable
        }

        $responseToPatch->setNumberLikes($increment);

        $this->dataManager->persist($responseToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response numberLikes Unincrement successfully', 'idTweet' => $responseToPatch->getId()]);
    }

    #[Route("api/response/incrementLikes/{id}", name: "incrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name:"Response")]
    public function incrementLikes(int $id): JsonResponse
    {

        $responseToPatch = $this->responseRepository->find($id);

        if (!$responseToPatch) {
            return $this->json(['error' => 'Response not found'], 404);
        }

        $increment = $responseToPatch->getNumberLikes() + 1;
        $responseToPatch->setNumberLikes($increment);

        $this->dataManager->persist($responseToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response numberLikes increment successfully', 'idTweet' => $responseToPatch->getId()]);
    }
}