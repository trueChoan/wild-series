<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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
    #[ParamConverter('program', options: ['mapping' => ['programId' => 'id']])]
    #[ParamConverter('season', options: ['mapping' => ['seasonId' => 'id']])]
    public function showSeason(
        Program $program,
        Season $season,
        int $seasonId,
        SeasonRepository $seasonRepository,
        EpisodeRepository $episodeRepository
    ): Response {

        if (!$seasonId) {
            throw $this->createNotFoundException(
                'Pas de saison avec id : ' . $seasonId . ' trouvée dans la table'
            );
        }
        $seasonTotal = $program->getSeasonNumber();

        $seasonNumber = $season->getNumber();
        $seasonPrev = $seasonRepository->findBy(['number' => $seasonNumber - 1], null, 1) ?? null;
        $seasonNext = $seasonRepository->findBy(['number' => $seasonNumber + 1], null, 1) ?? null;

        if (isset($seasonNext[0]) && $seasonNext[0]->getNumber() > $seasonTotal) {
            $seasonNext = null;
        }
        return $this->render('program/season.html.twig', [
            'program' => $program,
            'season' => $season,
            'season_prev' => $seasonPrev,
            'season_next' => $seasonNext,
            'episodes' => $episodeRepository->findAll()
        ]);
    }
    #[Route('/{program_id<\d+>}/season/{season_id<\d+>}/episode/{episode_id<\d+>}', name: 'episode_show')]
    #[ParamConverter('program', options: ['mapping' => ['program_id' => 'id']])]
    #[ParamConverter('season', options: ['mapping' => ['season_id' => 'id']])]
    #[ParamConverter('episode', options: ['mapping' => ['episode_id' => 'id']])]
    public function showEpisode(
        Program $program,
        Season $season,
        Episode $episode,
        EpisodeRepository $episodeRepository,
        int $episode_id
    ) {
        if (!$episode_id) {
            throw $this->createNotFoundException(
                'Pas d\'épisode avec id : ' . $episode_id . ' trouvée dans la table'
            );
        }
        $episodeNumber = $episode->getNumber();
        $epPrev = $episodeRepository->findBy(['number' => $episodeNumber - 1], null, 1) ?? null;
        $epNext = $episodeRepository->findBy(['number' => $episodeNumber + 1], null, 1) ?? null;
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'ep_prev' => $epPrev,
            'ep_next' => $epNext
        ]);
    }

    #[Route('/new', name: 'new')]
    public function newProgram(Request $request, ProgramRepository $programRepository): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $programRepository->add($program, true);
            return $this->redirectToRoute('program_index');
        }
        return $this->renderForm('program/new.html.twig', [
            'form' => $form
        ]);
    }
}
