<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Repository\TweetRepository;
use App\Repository\UserAccountRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class TweetController extends AbstractController
{
    private $tweetRepository;

    private $userRepository;

    private $serializer;

    private $dataManager;

    public function __construct(TweetRepository $tweetRepository, UserAccountRepository $userRepository, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;

        $this->dataManager = $manager;

        $this->serializer = $serializer;

    }

    #[Route("api/tweet", name: "createTweet", methods: ['POST'])]
    public function createTweet(Request $request) : JsonResponse{
        $data = json_decode($request->getContent(), true);

        $tweetToSave = new Tweet();

        $tweetToSave->setContent($data["content"]);
        $tweetToSave->setNumberLikes($data["numberLikes"]);

        $userId = $data["idUser"];
        $userAccount = $this->userRepository->find($userId);

        if (!$userAccount) {

            return $this->json(['error' => 'User not found'], 404);
        }

        $tweetToSave->setUser($userAccount);


        $this->dataManager->persist($tweetToSave);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet created successfully', 'idTweet' => $tweetToSave->getId()]);
    }

    #[Route("api/tweet/{id}", name: "deleteTweet", methods: ['DELETE'])]
    public function deleteTweet(int $id) : JsonResponse{

        $tweetToDelete = $this->tweetRepository->find($id);

        if(!$tweetToDelete){
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $this->dataManager->remove($tweetToDelete);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet remove successfully', 'idTweet' => $id]);
    }

    #[Route("api/tweet/incrementLikes/{id}", name :"incrementLikes", methods: ['PATCH'])]
    public function incrementLikes(int $id) : JsonResponse {

        $tweetToPatch = $this->tweetRepository->find($id);

        if(!$tweetToPatch){
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $increment = $tweetToPatch->getNumberLikes() + 1;
        $tweetToPatch->setNumberLikes($increment);

        $this->dataManager->persist($tweetToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet numberLikes increment successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

    #[Route("api/tweet/unincrementLikes/{id}", name :"unincrementLikes", methods: ['PATCH'])]
    public function unincrementLikes(int $id) : JsonResponse {

        $tweetToPatch = $this->tweetRepository->find($id);

        if(!$tweetToPatch){
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $increment = $tweetToPatch->getNumberLikes() - 1;

        if($increment < 0){
            return $this->json(['error' => 'Tweet has already numberLikes of 0'], 406); #Not acceptable
        }

        $tweetToPatch->setNumberLikes($increment);

        $this->dataManager->persist($tweetToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet numberLikes Unincrement successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

        #[Route("api/tweet/{idTweet}/responses")]
        public function getResponsesByTweet(int $idTweet): JsonResponse
        {
            $tweet = $this->tweetRepository->findOneByIdWithResponses($idTweet);

            if(!$tweet){
                return $this->json(['error' => 'Tweet not found'], 404);
            }

            $tweetData = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'responses' => []
            ];

            foreach ($tweet->getResponses() as $response){

                $userData = [
                    'id' => $response->getUserAccount()->getId(),
                    'firstName' => $response->getUserAccount()->getFirstName(),
                    'lastName' => $response->getUserAccount()->getLastName(),
                ];

                $responseData = [
                    'id' => $response->getId(),
                    'content' => $response->getContent(),
                    'numberLikes' => $response->getNumberLikes(),
                    'user' => $userData
                ];

                $tweetData['responses'][] = $responseData;
            }
            $jsonData = $this->serializer->serialize($tweetData, 'json');

            $response = new JsonResponse($jsonData, 200, [], true);

            return $response;

        }
}