<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function list(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->buildDataResponse($categories);
    }

    public function update(Request $request, int $id) : Response
    {
        $category = $this->categoryRepository->find($id);

        if ($category === null) {
            return $this->buildNotFoundResponse();
        }

        $form = $this->createForm(
            CategoryType::class,
            $category,
            ['method' => 'PUT']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);

        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $this->categoryRepository->save($category, true);

        return $this->buildDataResponse($category);
    }

    public function add(Request $request) : Response
    {
        $category = new Category();

        $form = $this->createForm(
            CategoryType::class,
            $category,
            ['method' => 'POST']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);

        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $this->categoryRepository->save($category, true);

        return $this->buildDataResponse($category);
    }

    public function delete(int $id) : Response
    {
        $category = $this->categoryRepository->find($id);

        if ($category === null) {
            return $this->buildNotFoundResponse();
        }

        $this->categoryRepository->remove($category, true);

        return $this->buildEmptyResponse();
    }
}
