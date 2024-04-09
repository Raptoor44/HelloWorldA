<?php

namespace App\Controller;

use App\Repository\LogRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class LogController extends AbstractController
{
    private $userService;

    private $logRepository;
    public function __construct(LogRepository $logRepository)
    {
        $this->userService = new UserService();

        $this->logRepository = $logRepository;
    }
}