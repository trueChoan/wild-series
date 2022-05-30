<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render(
            'index.html.twig',
            ['programs' => $programRepository->getLastPrograms()]
        );
    }
}
