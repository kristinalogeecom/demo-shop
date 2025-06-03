<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\DTO\Product;
use DemoShop\Application\BusinessLogic\ServiceInterface\ProductServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\Exception;
use DemoShop\Infrastructure\Exception\ImageException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles HTTP requests related to product management in the admin panel.
 */
class ProductController
{
    /**
     * Renders the product table page.
     *
     * @return HtmlResponse
     */
    public function getProductsHtml(): HtmlResponse
    {
        return new HtmlResponse('Products');
    }

    /**
     * Retrieves paginated and filtered products based on provided filter options.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ServiceNotFoundException
     */
    public function getProducts(Request $request): Response
    {
        $page = (int)($request->query('page') ?? 1);

        $filters = [
            'q' => $request->query('q'),
            'category_id' => $request->query('category_id'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
        ];

        $products = $this->getProductService()->getFilteredProducts($filters, $page);
        return new JsonResponse($products);
    }

    /**
     * Displays the form for creating or editing a product.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function renderProductForm(Request $request): Response
    {
        return new HtmlResponse('ProductForm');
    }

    /**
     * Validates and saves a product from submitted form data.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveProduct(Request $request): Response
    {
        $data = $request->all();

        if (isset($data['category'])) {
            $data['category_id'] = $data['category'];
        }

        $image = $request->file('image');

        try {
            if ($image !== null) {
                $data['image_path'] = $this->getProductService()->handleImageUpload($image);
            }

            $productDTO = new Product($data);
            $savedProduct = $this->getProductService()->saveProduct($productDTO);

            if (!$savedProduct) {
                return new JsonResponse(['error' => 'Failed to save product.'], 500);
            }

            return new JsonResponse(['success' => true, 'id' => $savedProduct->id]);

        } catch (ImageException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->getErrors()], 422);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => ['Unexpected error: ' . $e->getMessage()]], 500);
        }
    }

    /**
     * Deletes a single product by ID.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ServiceNotFoundException
     */
    public function deleteProduct(Request $request): Response
    {
        $id = (int) $request->param('id');

        $deleted = $this->getProductService()->deleteProductById($id);

        if (!$deleted) {
            return new JsonResponse(['error' => 'Product not found or could not be deleted.'], 404);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * Deletes multiple products by their IDs.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ServiceNotFoundException
     */
    public function deleteBatch(Request $request): Response
    {
        $ids = $request->all()['ids'] ?? [];

        if (!is_array($ids) || empty($ids)) {
            return new JsonResponse(['error' => 'No IDs provided.'], 400);
        }

        $deletedCount = $this->getProductService()->deleteMultipleProducts($ids);

        return new JsonResponse(['success' => true, 'deleted' => $deletedCount]);
    }

    /**
     * Updates the enabled status of multiple products.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ServiceNotFoundException
     */
    public function updateEnabledStatus(Request $request): Response
    {
        $data = $request->all();
        $ids = $data['ids'] ?? [];
        $enabled = $data['enabled'] ?? null;

        if(!is_array($ids) || !is_bool($enabled)) {
            return new JsonResponse(['error' => 'Invalid data.'], 400);
        }

        $this->getProductService()->updateEnabledStatus($ids, $enabled);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @return ProductServiceInterface
     *
     * @throws ServiceNotFoundException
     */
    private function getProductService(): ProductServiceInterface
    {
        return ServiceRegistry::get(ProductServiceInterface::class);
    }
}
