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

    #[Route("/tweet", name: "createTweet", methods: ['POST'])]
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

}