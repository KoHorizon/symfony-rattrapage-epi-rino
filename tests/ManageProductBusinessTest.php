<?php

namespace App\Tests;

use App\Business\ManageProductBusiness;
use App\Entity\Category;
use App\Form\Model\ProductModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ManageProductBusinessTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $name = 'product name';
        $description = 'lorem ipsum .....';

        self::bootKernel();
        $manageProductBusiness = static::getContainer()->get(ManageProductBusiness::class);

        $category = new Category();
        $category->setName('category');

        $model = new ProductModel();
        $model->setName($name);
        $model->setCategory($category);
        $model->setDescription('lorem ipsum .....');
        $model->setQuantity(3);
        $model->setVat(19.6);
        $model->setShortDescription('lorem...');
        $model->setPrice(2300);

        $product = $manageProductBusiness->create($model);

        $this->assertSame($name, $product->getName());
        $this->assertSame($description, $product->getDescription());

    }
}
