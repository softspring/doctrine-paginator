<?php

namespace Softspring\Component\DoctrinePaginator;

use Doctrine\ORM\QueryBuilder;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Softspring\Component\DoctrinePaginator\Form\PaginatorFormInterface;
use Softspring\Component\DoctrineQueryFilters\FilterFormInterface;
use Softspring\Component\DoctrineQueryFilters\Filters;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    public static function queryForm(QueryBuilder $qb, FormInterface $form, Request $request): PaginatedCollection
    {
        $reflectionClass = new \ReflectionClass($form->getConfig()->getType());
        if ($reflectionClass->implementsInterface(FilterFormInterface::class)) {
            Filters::applyForm($qb, $form, $request);
        }

        /** @var PaginatorFormInterface $paginatorForm */
        $paginatorForm = $form->getConfig()->getType();

        $page = $paginatorForm::getPage($request);
        $rpp = $paginatorForm::getRpp($request);
        $orderBy = $paginatorForm::getOrder($request);

        self::queryPage($qb, $page, $rpp, $orderBy);
    }

    public static function queryPage(QueryBuilder $qb, int $page, int $rpp = 0, array $orderBy = []): PaginatedCollection
    {
        $qb->setFirstResult(($page - 1) * $rpp);
        $qb->setMaxResults($rpp);

        foreach ($orderBy as $field => $order) {
//            if (preg_match('/^[a-z0-9][a-z0-9\_]+$/i', $field)) {
//                $qb->addOrderBy(($entityAlias?$entityAlias.'.':'').$field, $order);
//            } else {
            $qb->addOrderBy($field, $order);
//            }
        }

        $result = $qb->getQuery()->getResult();

        return new PaginatedCollection($result, $page, $rpp);
    }
}