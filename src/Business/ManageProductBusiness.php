<?php

namespace App\Business;

use App\Entity\Product;
use App\Form\Model\ProductModel;
use App\Repository\ProductRepository;

class ManageProductBusiness
{
    private ProductRepository $productRepository;

    /**
     * ManageProductBusiness constructor.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductModel $model
     *
     * @return Product
     */
    public function create(ProductModel $model): Product
    {
        $product = new Product();
        $product = $this->setValues($model, $product);

        return $product;
    }

    /**
     * @param ProductModel $model
     * @param Product      $product
     *
     * @return Product
     */
    public function update(ProductModel $model, Product $product): Product
    {
        return $this->setValues($model, $product);
    }


    /**
     * @param ProductModel $model
     * @param Product      $product
     *
     * @return Product
     */
    private function setValues(ProductModel $model, Product $product): Product
    {
        $product
            ->setName($model->getName())
            ->setShortDescription($model->getShortDescription())
            ->setDescription($model->getDescription())
            ->setPrice($model->getPrice())
            ->setQuantity($model->getQuantity())
            ->setCategory($model->getCategory())
            ->setVat($model->getVat())
        ;

        return $product;
    }
}
