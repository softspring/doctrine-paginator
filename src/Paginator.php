<?php

namespace Softspring\Component\DoctrinePaginator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Softspring\Component\DoctrinePaginator\Form\PaginatorFormInterface;
use Softspring\Component\DoctrineQueryFilters\Exception\InvalidFilterValueException;
use Softspring\Component\DoctrineQueryFilters\Exception\MissingFromInQueryBuilderException;
use Softspring\Component\DoctrineQueryFilters\FilterFormInterface;
use Softspring\Component\DoctrineQueryFilters\Filters;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    public static function queryForm(QueryBuilder $qb, FormInterface $form, Request $request): PaginatedCollection
    {
//        $reflectionClass = new \ReflectionClass($form->getConfig()->getType());
//        if ($reflectionClass->implementsInterface(FilterFormInterface::class)) {
//            Filters::applyForm($qb, $form, $request);
//        }
//
//        /** @var PaginatorFormInterface $paginatorForm */
//        $paginatorForm = $form->getConfig()->getType();
//
//        $page = $paginatorForm::getPage($request);
//        $rpp = $paginatorForm::getRpp($request);
//        $orderBy = $paginatorForm::getOrder($request);
//
//        self::queryPage($qb, $page, $rpp, $orderBy);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidFilterValueException
     * @throws MissingFromInQueryBuilderException
     */
    public static function queryPage(QueryBuilder $qb, int $page, int $rpp = null, array $filters = [], array $orderBy = []): PaginatedCollection
    {
        // get total results
        $cloneQb = clone $qb;
        Filters::apply($cloneQb, $filters);
        $cloneQb->select('COUNT('.$cloneQb->getAllAliases()[0].')');
        $total = (int)$cloneQb->getQuery()->getSingleScalarResult();

        if ($total) {
            $qb->setFirstResult(($page - 1) * $rpp);
            $qb->setMaxResults($rpp);
            Filters::apply($qb, $filters);
            Filters::sortBy($qb, $orderBy);
            $result = $qb->getQuery()->getResult();
        }

        return new PaginatedCollection(new ArrayCollection($result??[]), $page, $rpp, $total, $orderBy);
    }
}