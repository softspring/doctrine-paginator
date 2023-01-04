<?php

namespace Softspring\Component\DoctrinePaginator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Softspring\Component\DoctrinePaginator\Exception\InvalidFormTypeException;
use Softspring\Component\DoctrinePaginator\Form\PaginatorFormInterface;
use Softspring\Component\DoctrineQueryFilters\Exception\InvalidFilterValueException;
use Softspring\Component\DoctrineQueryFilters\Exception\MissingFromInQueryBuilderException;
use Softspring\Component\DoctrineQueryFilters\Filters;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidFilterValueException
     * @throws MissingFromInQueryBuilderException
     */
    public static function queryPage(QueryBuilder $qb, int $page, int $rpp, array $filters = [], array $orderBy = [], int $filtersMode = Filters::MODE_AND): PaginatedCollection
    {
        // get total results
        $countQb = clone $qb;
        Filters::apply($countQb, $filters, $filtersMode);
        $countQb->select('COUNT('.$countQb->getAllAliases()[0].')');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        if ($total) {
            $qb->setFirstResult(($page - 1) * $rpp);
            $qb->setMaxResults($rpp);
            Filters::apply($qb, $filters, $filtersMode);
            Filters::sortBy($qb, $orderBy);
            $result = $qb->getQuery()->getResult();
        }

        return new PaginatedCollection(new ArrayCollection($result ?? []), $page, $rpp, $total, $orderBy);
    }

    /**
     * @throws InvalidFormTypeException
     */
    public static function processPaginatedFilterForm(FormInterface $form, Request $request): array
    {
        if (!$form->getConfig()->getType()->getInnerType() instanceof PaginatorFormInterface) {
            throw new InvalidFormTypeException();
        }

        $formCompiledOptions = $form->getConfig()->getOptions();

        $page = $request->get($formCompiledOptions['page_field_name'], 1);

        $rpp = $form->get($formCompiledOptions['rpp_field_name'])->getData() ?? $formCompiledOptions['rpp_default_value'];
        if (!in_array($rpp, $formCompiledOptions['rpp_valid_values'])) {
            $rpp = $formCompiledOptions['rpp_default_value'];
        }

        $order = $form->get($formCompiledOptions['order_field_name'])->getData() ?? $formCompiledOptions['order_default_value'];
        if (!in_array($order, $formCompiledOptions['order_valid_fields'])) {
            $order = $formCompiledOptions['order_default_value'];
        }
        $orderKey = array_search($order, $formCompiledOptions['order_valid_fields']);
        if (is_string($orderKey)) {
            $order = $orderKey;
        }

        $direction = $form->get($formCompiledOptions['order_direction_field_name'])->getData() ?? $formCompiledOptions['order_direction_default_value'];
        if (!in_array($direction, $formCompiledOptions['order_direction_valid_fields'])) {
            $direction = $formCompiledOptions['order_direction_default_value'];
        }

        $orderSort = [$order => $direction];

        $filters = $form->isSubmitted() && $form->isValid() ? array_filter($form->getData()) : [];

        $qb = $formCompiledOptions['query_builder'];
        $filtersMode = $formCompiledOptions['query_builder_mode'];

        return [$qb, $page, $rpp, $filters, $orderSort, $filtersMode];
    }

    /**
     * @throws InvalidFilterValueException
     * @throws InvalidFormTypeException
     * @throws MissingFromInQueryBuilderException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public static function queryPaginatedFilterForm(FormInterface $form, Request $request): PaginatedCollection
    {
        [$qb, $page, $rpp, $filters, $orderSort, $filtersMode] = self::processPaginatedFilterForm($form, $request);

        return self::queryPage($qb, $page, $rpp, $filters, $orderSort, $filtersMode);
    }
}
