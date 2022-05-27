<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(int $id = 77, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $categories = $categoryRepository->findOneById($id);
        if (!$categories) {
            throw      $this->createNotFoundException(
                'Category not found with name : ' . $id . ' in category\'s table'
            );
        }
        $programs = $programRepository->getProgramByCategory($id);

        return $this->render('category/show.html.twig',  [
            'categories' => $categories,
            'programs' => $programs
        ]);
    }
}
