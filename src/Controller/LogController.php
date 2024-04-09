<?php

namespace App\Controller;

use App\Repository\LogRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LogController extends AbstractController
{
    private $userService;

    private $logRepository;
    public function __construct(LogRepository $logRepository)
    {
        $this->userService = new UserService();

        $this->logRepository = $logRepository;
    }

    /**
     *
     * Cette route permet de récupérer tous les logs.
     * Seul un administrateur peut récupérer la liste des logs.
     *
     */
    #[Route("api/logs", name: "getAllLogs", methods: ['GET'])]
    #[OA\Tag(name: "Log")]
    #[OA\Response(
        response: 200,
        description: 'La liste des logs en base de données.',
    )]
    public function getAllLogs(TokenInterface $token): JsonResponse
    {
        $logs = $this->logRepository->findAllWithoutOtherAttributes();

        if (count($logs) != 0) {

            $user = $this->userService->GetUserWithTokenInterface($token);
            if(in_array("ADMIN", $user->getRoles())){
                 return $this->json(['tweets' => $logs], 200);
            }else{
                return $this->json(["error" => "You don't have permission"], 403);
            }

        } else {
            return $this->json(["error" => "No responses are find in database"], 204);
        }
    }
}