<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\User;
use App\Service\Slugify;
use App\Form\ProgramType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mime\Email;
use App\Service\UserService;

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

    #[Route('/{slug}', methods: ['GET'], name: 'show')]
    #[ParamConverter('program', options: ['mapping' => ['slug' => 'slug']])]
    public function show(
        $slug,
        ProgramRepository $programRepository,
        SeasonRepository $seasonRepository,
    ): Response {
        $program = $programRepository->findOneBySlug(['slug' => $slug]);
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de Série avec titre : ' . $slug . ' trouvé dans la table'
            );
        }
        return $this->render('program/show.html.twig', [
            'slug' => $slug,
            'program' => $program,
            'seasons' => $seasonRepository->findAll()
        ]);
    }

    #[Route('/{slug}/season/{season_slug}', methods: ['GET'], name: 'season_show')]
    #[ParamConverter('program', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('season', options: ['mapping' => ['season_slug' => 'slug']])]
    public function showSeason(
        Program $program,
        Season $season,

        SeasonRepository $seasonRepository,
        EpisodeRepository $episodeRepository
    ): Response {

        if (!$season) {
            throw $this->createNotFoundException(
                'Pas de saison  : ' . $season . ' trouvée dans la table'
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
    #[Route('/{program_slug}/season/{season_slug}/episode/{episode_slug}',  methods: ['GET', 'POST'], name: 'episode_show')]
    #[ParamConverter('program', options: ['mapping' => ['program_slug' => 'slug']])]
    #[ParamConverter('season', options: ['mapping' => ['season_slug' => 'slug']])]
    #[ParamConverter('episode', options: ['mapping' => ['episode_slug' => 'slug']])]
    public function showEpisode(
        Request $request,
        Program $program,
        Season $season,
        Episode $episode,
        EpisodeRepository $episodeRepository,
        CommentRepository $commentRepository,
        UserService $userService
    ) {
        if (!$episode) {
            throw $this->createNotFoundException(
                'Pas d\'épisode : ' . $episode . ' trouvée dans la table'
            );
        }
        $user = $userService->getUser();
        $comment = new Comment($episode, $user);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentRepository->add($comment, true);

            return $this->redirectToRoute('program_episode_show');
        }

        $episodeNumber = $episode->getNumber();
        $epPrev = $episodeRepository->findBy(['number' => $episodeNumber - 1], null, 1) ?? null;
        $epNext = $episodeRepository->findBy(['number' => $episodeNumber + 1], null, 1) ?? null;
        return $this->renderForm('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'ep_prev' => $epPrev,
            'ep_next' => $epNext,
            'form' => $form,
            'comments' => $commentRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'new', priority: 1)]
    public function newProgram(
        Request $request,
        ProgramRepository $programRepository,
        Slugify $slugify,
        MailerInterface $mailer
    ): Response {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $programRepository->add($program, true);
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('tierrylermite@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', [
                    'program' => $program
                ]));
            $mailer->send($email);
            return $this->redirectToRoute('program_index');
        }
        return $this->renderForm('program/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->add($program, true);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }
}
