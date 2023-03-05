<?php

namespace App\Controller;

use App\Business\ManageProductBusiness;
use App\Entity\Cart;
use App\Entity\Product;
use App\Form\Model\AbstractPaginationModel;
use App\Form\Model\ProductModel;
use App\Form\Model\ProductSearchModel;
use App\Form\Type\ProductSearchType;
use App\Form\Type\ProductType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    const CONTEXT = 'product';

    private ProductRepository $productRepository;
    private CartRepository $cartRepository;

    public function __construct(ProductRepository $productRepository, CartRepository $cartRepository)
    {
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Get one product resource from its unique identifier",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Product not found",
     * ),
     *  @OA\Response(
     *     response=401,
     *     description= "Authentication error, you are not allowed to access this url"
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function single(Product $product): Response
    {
        return $this->buildDataResponse($product, self::CONTEXT);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Get a list of product resources",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * ),
     * @OA\Response(
     *     response=204,
     *     description="Success, but no product found",
     * ),
     *  @OA\Response(
     *     response=401,
     *     description= "Authentication error, you are not allowed to access this url"
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     *
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return JsonResponse
     */
    public function list(Request $request, PaginatorInterface $paginator) : Response
    {
        $model = new ProductSearchModel();

        $form = $this->createForm(
            ProductSearchType::class,
            $model,
            ['method' => 'GET']
        );

        $form->submit($request->query->all());
        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $products = $this->productRepository->searchFilter($model);

//        $products = $paginator->paginate(
//            $query,
//            ($model->getPage() !== null) ? $model->getPage() : AbstractPaginationModel::DEFAULT_PAGE,
//            ($model->getLimit() !== null) ? $model->getLimit() : AbstractPaginationModel::DEFAULT_LIMIT,
//        );

        return $this->buildDataResponse($products, self::CONTEXT);
    }


    /**
     * @OA\Response(
     *     response=201,
     *     description="Create a Complainant resources",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * ),
     *  @OA\Response(
     *     response=401,
     *     description= "Authentication error, you are not allowed to access this url"
     * ),
     *  @OA\Response(
     *     response=400,
     *     description= "Form error"
     * ),
     * @OA\RequestBody(
     *      @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ProductType::class)
     *      )
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param ManageProductBusiness $manageProductBusiness
     * @return Response
     */
    public function add(Request $request, ManageProductBusiness $manageProductBusiness): Response
    {
        $model = new ProductModel();

        $form = $this->createForm(
            ProductType::class,
            $model,
            ['method' => 'POST']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);
        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $product = $manageProductBusiness->create($model);
        $this->productRepository->save($product, true);

        return $this->buildDataResponse($product, self::CONTEXT);

    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Modify an Complainant resources",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * ),
     *  @OA\Response(
     *     response=401,
     *     description= "Authentication error, you are not allowed to access this url"
     * ),
     *  @OA\Response(
     *     response=400,
     *     description= "Form error"
     * ),
     * @OA\RequestBody(
     *      @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ProductType::class)
     *      )
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param Product $product
     * @param ManageProductBusiness $manageProductBusiness
     * @return Response
     */
    public function update(Request $request, Product $product, ManageProductBusiness $manageProductBusiness): Response
    {
        $model = new ProductModel();
        $form = $this->createForm(
            ProductType::class,
            $model,
            ['method' => 'PUT']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);
        if (!$form->isValid()) {
            return $this->json(null, );
        }

        try {
            $manageProductBusiness->update($model, $product);
            $this->productRepository->save($product);

            return $this->buildDataResponse($product, self::CONTEXT);

        } catch (Exception $exception) {
            return $this->buildErrorResponse($exception);
        }
    }

    /**
     * @OA\Response(
     *     response=204,
     *     description="Remove product resource from its unique identifier",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Product not found",
     * ),
     *  @OA\Response(
     *     response=401,
     *     description= "Authentication error, you are not allowed to access this url"
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function delete(Product $product): Response
    {
        $this->productRepository->remove($product, true);
        return $this->buildEmptyResponse();
    }

    public function addToCart(Product $product): Response
    {
        $cart = new Cart();
        $cart->addProduct($product);
        $cart->addTotalAmount($product->getPrice());
        $product->removeQuantity(1);

        $this->cartRepository->save($cart, true);
    }
}
