<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series',
            'programs' => $programRepository->findAll()
        ]);
    }

    #[Route('/{id}', methods: ['GET'], name: 'show', requirements: ['id' => '\d+'])]
    public function show($id = 1, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de Série avec id : ' . $id . ' trouvé dans la table'
            );
        }
        return $this->render('program/show.html.twig', [
            'id' => $id,
            'program' => $program
        ]);
    }
}
