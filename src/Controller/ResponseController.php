<?php

namespace App\Controller;

use App\Entity\Response;
use App\Entity\Tweet;
use App\Entity\UserAccount;
use App\Repository\ResponseRepository;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResponseController extends AbstractController
{
    private $responseRepository;
    private $serializer;
    private $tweetRepository;
    private $dataManager;
    private $validator;


    public function __construct(ResponseRepository     $responseRepositoryParam,
                                TweetRepository        $tweetRepository,
                                SerializerInterface    $serializerParam,
                                EntityManagerInterface $managerParam,
                                ValidatorInterface     $validator)
    {
        $this->responseRepository = $responseRepositoryParam;
        $this->tweetRepository = $tweetRepository;

        $this->serializer = $serializerParam;

        $this->dataManager = $managerParam;

        $this->validator = $validator;
    }

    /**
     *
     * Cette route permet de créer une réponse à tweet.
     *
     */
    #[Route("api/reponse", methods: ['POST'])]
    #[OA\Tag(name:"Response")]
    #[OA\Parameter(
        name: 'user',
        description: "La réponse avec ses différents paramètres.",
        in: 'query',
        required: true
    )]
    #[OA\Response(
        response: 202,
        description: "La réponse a bien été créer.",
    )]
    public function createResponse(Request $request, TokenInterface $token): JsonResponse
    {
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

        if (!$tweetToSave) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $responseToSave->setTweet($tweetToSave);

        $errors = $this->validator->validate($responseToSave);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return $this->json(['Error' => 'Response not register in database', $errorsString]);
        }

        $this->dataManager->persist($responseToSave);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response created successfully', 'idResponse' => $responseToSave->getId()]);

    }

    /**
     *
     * Cette route permet de supprimer une réponse à tweet.
     *
     */
    #[Route("api/response/{id}", name: "deleteResponse", methods: ['DELETE'])]
    #[OA\Tag(name: "Response")]
    #[OA\Response(
        response: 202,
        description: "Le code de confirmation de suppression.",
    )]
    #[Security(name: 'Bearer')]
    public function deleteResponse(int $id, TokenInterface $token): JsonResponse
    {

        $responseToDelete = $this->responseRepository->find($id);

        $user = $token->getUser();

        if (!($user instanceof UserAccount)) {
            $user = UserAccount::convertFrom($user);
        }

        if (!$responseToDelete) {
            return $this->json(['error' => 'response not found'], 403);
        }

        if ($user->getId() === $responseToDelete->getUserAccount()->getId() || in_array("ADMIN", $user->getRoles())) {
            $this->dataManager->remove($responseToDelete);
            $this->dataManager->flush();

            return $this->json(['message' => "Response remove successfully", 'idResponse' => $id]);
        }

        return $this->json(['error' => 'Access Denied'], 401);#Non autorisé
    }


    /**
     *
     * Cette route permet de désincrementer le nombre de like d'une reponse.
     *
     */
    #[Route("api/response/unincrementLikes/{id}", name: "unincrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name: "Response")]
    #[OA\Response(
        response: 202,
        description: "Cette route permet de désincrementer le nombre de like d'une réponse",
    )]
    #[Security(name: 'Bearer')]
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

    /**
     *
     * Cette route permet d'incrémenter nombre de j'aime d'une réponse.
     *
     */
    #[Route("api/response/incrementLikes/{id}", name: "incrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name: "Response")]
    #[OA\Response(
        response: 202,
        description: "Cette route permet d'incrémenter le nombre de like d'une réponse",
    )]
    #[Security(name: 'Bearer')]
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