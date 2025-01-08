<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Doctrine\ORM\QueryBuilder;

interface QueryBuilderProcessorInterface
{
    public function preProcessQueryBuilder(QueryBuilder $qb, array &$filters, array &$orderSort, int &$filtersMode): QueryBuilder;
}
