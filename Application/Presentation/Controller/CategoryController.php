<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;
use Exception;
use Throwable;


/**
 * Handles category-related HTTP requests for the admin panel.
 */
class CategoryController
{

    /**
     * Retrieves all categories in a tree structure.
     *
     * @param Request $request
     *
     * @return Response JSON response with the category tree or an error.
     */
    public function getCategories(Request $request): Response
    {
        try {
            return new JsonResponse($this->categoryService()->getAllCategories());
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves all categories in a flat list.
     *
     * @param Request $request
     *
     * @return Response JSON response with a flat list of categories or an error.
     */
    public function getFlatCategories(Request $request): Response
    {
        try {
            return new JsonResponse($this->categoryService()->getFlatCategories());
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieves a single category by ID.
     *
     * @param Request $request
     *
     * @return Response JSON response with the category or an error.
     */
    public function getCategory(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (!$id) {
            return new JsonResponse(['error' => 'Category ID is missing.'], 400);
        }

        try {
            return new JsonResponse($this->categoryService()->getCategoryById($id));
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

            $saved = $this->categoryService()->saveCategory($category);

            return new JsonResponse([
                'success' => true,
                'category' => $saved->toArray()
            ]);
        } catch (Throwable $e) {
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
            $this->categoryService()->deleteCategory($id);
            return new JsonResponse(['success' => true]);
        } catch (Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Retrieves descendant categories IDs in a list.
     *
     * @param Request $request
     *
     * @return Response JSON response with a list of descendant IDs
     * @throws Exception
     */
    public function getDescendantIds(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (!$id) {
            return new JsonResponse(['error' => 'Category ID is missing.'], 400);
        }

        return new JsonResponse($this->categoryService()->getDescendantIds((int) $id));
    }

    /**
     * @throws Exception
     */
    private function categoryService(): CategoryServiceInterface
    {
        return ServiceRegistry::get(CategoryServiceInterface::class);
    }

}
