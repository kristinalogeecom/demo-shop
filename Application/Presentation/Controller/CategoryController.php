<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;
use Exception;


/**
 * Handles category-related HTTP requests for the admin panel.
 */
class CategoryController
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Retrieves all categories in a tree structure.
     *
     * @return Response JSON response with the category tree or an error.
     */
    public function getCategories(): Response
    {
        try {
            return new JsonResponse($this->categoryService->getAllCategories());
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves all categories in a flat list.
     *
     * @return Response JSON response with a flat list of categories or an error.
     */
    public function getFlatCategories(): Response
    {
        try {
            return new JsonResponse($this->categoryService->getFlatCategories());
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves a single category by ID.
     *
     * @param int $id The ID of the category to retrieve.
     *
     * @return Response JSON response with the category or an error.
     */
    public function getCategory(int $id): Response
    {
        try {
            return new JsonResponse($this->categoryService->getCategoryById($id));
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Saves (creates or updates) a category based on request input.
     *
     * @param Request $request The HTTP request containing category data.
     *
     * @return Response JSON response with the saved category or error details.
     */
    public function saveCategory(Request $request): Response
    {
        try {
            $category = CategoryModel::fromArray(
                $request->only(['id', 'parent_id', 'name', 'code', 'description'])
            );

            $saved = $this->categoryService->saveCategory($category);

            return new JsonResponse([
                'success' => true,
                'category' => $saved->toArray()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 400);
        }
    }

    /**
     * Deletes a category by ID provided in the request.
     *
     * @param Request $request The HTTP request containing the category ID.
     *
     * @return Response JSON response indicating success or failure.
     */
    public function deleteCategory(Request $request): Response
    {
        $id = $request->input('id');
        if (!$id) {
            return new JsonResponse(['error' => 'Category ID is required.'], 400);
        }

        try {
            $this->categoryService->deleteCategory($id);
            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
