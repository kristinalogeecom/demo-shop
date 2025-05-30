<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\Exception;
use DemoShop\Infrastructure\Exception\NotFoundException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\Response;


/**
 * Handles category-related HTTP requests for the admin panel.
 */
class CategoryController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function categoriesPage(Request $request): Response
    {
        return new HtmlResponse('Categories');
    }

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
            return new JsonResponse($this->getCategoryService()->getAllCategories());
        } catch (NotFoundException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 404);
        } catch (ServiceNotFoundException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 505);
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
            return new JsonResponse($this->getCategoryService()->getFlatCategories());
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (ServiceNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 505);
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
            return new JsonResponse($this->getCategoryService()->getCategoryById($id));
        }  catch (NotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (ServiceNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 505);
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

            $saved = $this->getCategoryService()->saveCategory($category);

            return new JsonResponse([
                'success' => true,
                'category' => $saved->toArray()
            ]);
        } catch (ValidationException $e) {
            return new JsonResponse([
                'errors' => $e->getErrors()
            ], 422);
        } catch (NotFoundException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'Unexpected error: ' . $e->getMessage()
            ], 500);
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
            $this->getCategoryService()->deleteCategory($id);
            return new JsonResponse(['success' => true]);
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->getErrors()], 422);
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (ServiceNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 505);
        }
    }


    /**
     * Retrieves descendant categories IDs in a list.
     *
     * @param Request $request
     *
     * @return Response JSON response with a list of descendant IDs
     */
    public function getDescendantIds(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (!$id) {
            return new JsonResponse(['error' => 'Category ID is missing.'], 400);
        }

        try {
            return new JsonResponse($this->getCategoryService()->getDescendantIds($id));
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (ServiceNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 505);
        }
    }

    /**
     * Retrieves the category service instance from the service container.
     *
     * @return CategoryServiceInterface
     *
     * @throws ServiceNotFoundException
     */
    private function getCategoryService(): CategoryServiceInterface
    {
        return ServiceRegistry::get(CategoryServiceInterface::class);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ServiceNotFoundException
     */
    public function renderCategoryDetails(Request $request): Response
    {
        $id = (int)$request->param('id');

        if (!$id) {
            return new JsonResponse(['error' => 'Missing ID'], 400);
        }

        try {
            $category = $this->getCategoryService()->getCategoryById($id);
            return new HtmlResponse('CategoryDetails', [
                'category' => $category
            ]);
        } catch (NotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function renderCategoryForm(Request $request): Response
    {
        $categoryId = $request->query('categoryId');
        $parentId = $request->query('parentId');

        $allCategories = $this->getCategoryService()->getFlatCategories();
        $excludeIds = [];

        if ($categoryId) {
            $descendantIds = $this->getCategoryService()->getDescendantIds((int)$categoryId);
            $excludeIds = array_map('intval', $descendantIds);
            $excludeIds[] = (int)$categoryId;
        }

        $filteredCategories = array_filter($allCategories, function ($cat) use ($excludeIds) {
            return !in_array((int)$cat['id'], $excludeIds, true);
        });

        $category = null;
        if ($categoryId) {
            $category = $this->getCategoryService()->getCategoryById((int)$categoryId);
        }

        return new HtmlResponse('CategoryForm', [
            'categoryId'     => $categoryId,
            'parentId'       => $parentId,
            'category'       => $category ?? [],
            'allCategories'  => array_values($filteredCategories),
        ]);
    }

    public function renderEmptyPanel(Request $request): Response
    {
        return new HtmlResponse('NoSelection');
    }


}
