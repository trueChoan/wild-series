<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Program;
use App\Entity\Episode;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
    public function show(
        $id,
        ProgramRepository $programRepository,
        SeasonRepository $seasonRepository,
        EpisodeRepository $episodeRepository
    ): Response {
        $program = $programRepository->findOneBy(['id' => $id]);
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de Série avec id : ' . $id . ' trouvé dans la table'
            );
        }
        return $this->render('program/show.html.twig', [
            'id' => $id,
            'program' => $program,
            'seasons' => $seasonRepository->findAll()
        ]);
    }

    #[Route('/{programId<\d+>}/season/{seasonId<\d+>}', methods: ['GET'], name: 'season_show')]
    #[Entity('program', options: ['id' => 'programId'])]
    #[Entity('season', options: ['id' => 'seasonId'])]
    public function showSeason(
        Program $programId,
        Season $seasonId,
        EpisodeRepository $episodeRepository
    ): Response {

        if (!$seasonId) {
            throw $this->createNotFoundException(
                'Pas de saison avec id : ' . $seasonId . ' trouvée dans la table'
            );
        }
        return $this->render('program/season.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episodes' => $episodeRepository->findAll()
        ]);
    }
    #[Route('/{program_id<\d+>}/season/{season_id<\d+>}/episode/{episode_id<\d+>}', name: 'episode_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', option: ['id' => 'season_id'])]
    #[Entity('episode', option: ['id' => 'episode_id'])]
    public function showEpisode(
        Program $program_id,
        Season $season_id,
        Episode $episode_id
    ) {
        if (!$episode_id) {
            throw $this->createNotFoundException(
                'Pas d\'épisode avec id : ' . $episode_id . ' trouvée dans la table'
            );
        }
        return $this->render('program/episode_show.html.twig', [
            'program' => $program_id,
            'season' => $season_id,
            'episode' => $episode_id
        ]);
    }
}
