<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Response;
use App\Entity\UserAccount;
use App\Repository\ResponseRepository;
use App\Repository\TweetRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
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

    private $userService;


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

        $this->userService = new UserService();
    }

    /**
     *
     * Cette route permet de récupérer la liste de toutes les réponses.
     *
     */
    #[Route("api/responses", name: "getAllResponses", methods: ['GET'])]
    #[OA\Tag(name: "Response")]
    #[OA\Response(
        response: 200,
        description: 'La liste des réponses en base de données.',
    )]
    public function getAllResponses(?TokenInterface $token = null): JsonResponse
    {
        $responses = $this->responseRepository->findAllWithoutOtherAttributes();

        if (count($responses) != 0) {

            #Partie enregistrement de log :

            $log = new Log();

            $log->setDateCreation(new \DateTime());
            $log->setControllerLibelle("TweetController");
            $log->setMethodLibelle("getAllResponses()");


            #Partie récupération user :

            $user = $this->userService->GetUserWithTokenInterface($token);
            $log->setIdUser($user);

            $contentGenerique = "Récuparation de toutes les réponses";

            if ($user) {
                $log->setContent($contentGenerique . " pour l'utilisateur" . $user->getId());
            } else {
                $log->setContent($contentGenerique . ".");
            }


            $this->dataManager->persist($log);
            $this->dataManager->flush();
            return $this->json(['tweets' => $responses], 200);
        } else {
            return $this->json(["error" => "No responses are find in database"], 204);
        }
    }

    /**
     *
     * Cette route permet de créer une réponse à tweet.
     *
     */
    #[Route("api/reponse", methods: ['POST'])]
    #[OA\Tag(name: "Response")]
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

        #Partie enregistrement de Reponse :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("ResponseController");
        $log->setMethodLibelle("createResponse()");
        $log->setContent("Enregistrement d'une réponse à un tweet. IdResponse : " . $responseToSave->getId()
            . " id du tweet : " . $responseToSave->getTweet()->getId()
            . " id de l'utilisateur ayant créer la réponse : " . $responseToSave->getUserAccount()->getId());

        #Partie récupération user :

        $user = $this->userService->GetUserWithTokenInterface($token);
        $log->setIdUser($user);

        $this->dataManager->persist($log);
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
    public function deleteResponse(int $id, TokenInterface $token): JsonResponse
    {

        $responseToDelete = $this->responseRepository->find($id);

        $user = $this->userService->GetUserWithTokenInterface($token);

        if (!$responseToDelete) {
            return $this->json(['error' => 'response not found'], 403);
        }

        if ($user->getId() === $responseToDelete->getUserAccount()->getId() || in_array("ADMIN", $user->getRoles())) {
            $this->dataManager->remove($responseToDelete);
            #Partie enregistrement de Reponse :

            $log = new Log();

            $log->setDateCreation(new \DateTime());
            $log->setControllerLibelle("ResponseController");
            $log->setMethodLibelle("deleteResponse()");
            $log->setContent("Suppression de la réponse ayant pour id : "
                . $responseToDelete->getId() . " id du tweet de la réponse : "
                . $responseToDelete->getTweet()->getId() . " id de l'utilisateur ayant créer la réponse : "
                . $responseToDelete->getUserAccount()->getId());

            #Partie récupération user :

            $user = $this->userService->GetUserWithTokenInterface($token);
            $log->setIdUser($user);

            $this->dataManager->persist($log);
            $this->dataManager->flush();;

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
    public function unincrementLikes(int $id, TokenInterface $token): JsonResponse
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
        #Partie enregistrement de Reponse :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("ResponseController");
        $log->setMethodLibelle("unincrementLikes()");
        $log->setContent("Désincrémentation de la réponse ayant pour id : " . $responseToPatch->getId());

        #Partie récupération user :

        $user = $this->userService->GetUserWithTokenInterface($token);
        $log->setIdUser($user);

        $this->dataManager->persist($log);
        $this->dataManager->flush();;

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
    public function incrementLikes(int $id, TokenInterface $token): JsonResponse
    {

        $responseToPatch = $this->responseRepository->find($id);

        if (!$responseToPatch) {
            return $this->json(['error' => 'Response not found'], 404);
        }

        $increment = $responseToPatch->getNumberLikes() + 1;
        $responseToPatch->setNumberLikes($increment);

        $this->dataManager->persist($responseToPatch);

        #Partie enregistrement de Reponse :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("ResponseController");
        $log->setMethodLibelle("unincrementLikes()");
        $log->setContent("Incrémentation de la réponse ayant pour id : " . $responseToPatch->getId());

        #Partie récupération user :

        $user = $this->userService->GetUserWithTokenInterface($token);
        $log->setIdUser($user);

        $this->dataManager->persist($log);
        $this->dataManager->flush();;
        return $this->json(['message' => 'Response numberLikes increment successfully', 'idTweet' => $responseToPatch->getId()]);
    }
}