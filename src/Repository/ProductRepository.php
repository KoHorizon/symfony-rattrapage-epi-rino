<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Model\ProductSearchModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filters(ProductSearchModel $model): Collection
    {
        return $this->filtersQueryBuilder($model)->getQuery()->getResult();
    }


    public function searchFilter(ProductSearchModel $model) : Collection
    {
        return $this
            ->createQueryBuilder($this->getAlias())
            ->andWhere($this->getAlias() . '.name like :nameLike')
            ->andWhere($this->getAlias() . '.price >= :priceMin')
            ->andWhere($this->getAlias() . '.price <= :priceMax')
            ->andWhere($this->getAlias() . '.category = :category')
            ->setParameters(
                [
                    'namelike' => '%' . $model->getName() . '%',
                    'priceMin' => $model->getPriceMin(),
                    'priceMax' => $model->getPriceMax(),
                    'category' => $model->getCategory(),
                ]
            )
            ->getQuery()
            ->getResult();
    }


    public function filtersQueryBuilder(ProductSearchModel $model): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $this->categoryJoin($queryBuilder);

        if ($model->getName()) {
            $this->nameLikeFilter($queryBuilder, $model->getName());
        }

        if ($model->getHasQuantity()) {
            $this->hasQuantityFilter($queryBuilder, $model->getHasQuantity());
        }

        if ($model->getCategory()) {
            $this->categoryFilter($queryBuilder, $model->getCategory());
        }

        if ($model->getPriceMin() && $model->getPriceMax()) {
            $this->pricingRangeFilter($queryBuilder, $model->getPriceMin(), $model->getPriceMax());
        }

        return $queryBuilder;
    }

    private function nameLikeFilter(QueryBuilder $queryBuilder, string $name) : void
    {
        $queryBuilder
            ->andWhere($this->getAlias() . '.name like :nameLike')
            ->setParameter('nameLike', '%' . $name . '%');
    }

    private function hasQuantityFilter(QueryBuilder $queryBuilder, bool $hasQuantity) : void
    {
        if ($hasQuantity === true) {
            $queryBuilder->andWhere($this->getAlias() . '.quantity > 0');
        }
    }

    private function pricingRangeFilter(QueryBuilder $queryBuilder, int $priceMin, int $priceMax) : void
    {
        $queryBuilder
            ->andWhere($this->getAlias() . '.price >= :priceMin')
            ->andWhere($this->getAlias() . '.price <= :priceMax')
            ->setParameter('priceMin', $priceMin)
            ->setParameter('priceMax', $priceMax);
    }

    private function categoryFilter(QueryBuilder $queryBuilder, Category $category) : void
    {
        $queryBuilder
            ->andWhere($this->getAlias() . '.category >= :category')
            ->setParameter('category', $category);
    }

    private function categoryJoin(QueryBuilder $queryBuilder) : void
    {
        $queryBuilder
            ->addSelect($this->getAlias() . '_category')
            ->leftJoin($this->getAlias() . '.category', $this->getAlias() . '_category');
    }

    public function getAlias(): string
    {
        return $this->getClassMetadata()->table['name'];
    }
}
