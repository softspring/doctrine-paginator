<?php

namespace Softspring\Component\DoctrinePaginator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Softspring\Component\DoctrineQueryFilters\Exception\InvalidFilterValueException;
use Softspring\Component\DoctrineQueryFilters\Exception\MissingFromInQueryBuilderException;
use Softspring\Component\DoctrineQueryFilters\Filters;

class Paginator
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidFilterValueException
     * @throws MissingFromInQueryBuilderException
     */
    public static function queryPage(QueryBuilder $qb, int $page, int $rpp = null, array $filters = [], array $orderBy = [], int $filtersMode = Filters::MODE_AND): PaginatedCollection
    {
        // get total results
        $cloneQb = clone $qb;
        Filters::apply($cloneQb, $filters, $filtersMode);
        $cloneQb->select('COUNT('.$cloneQb->getAllAliases()[0].')');
        $total = (int) $cloneQb->getQuery()->getSingleScalarResult();

        if ($total) {
            $qb->setFirstResult(($page - 1) * $rpp);
            $qb->setMaxResults($rpp);
            Filters::apply($qb, $filters, $filtersMode);
            Filters::sortBy($qb, $orderBy);
            $result = $qb->getQuery()->getResult();
        }

        return new PaginatedCollection(new ArrayCollection($result ?? []), $page, $rpp, $total, $orderBy);
    }
}
