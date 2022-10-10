<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Softspring\Component\DoctrineQueryFilters\FilterFormInterface;
use Softspring\Component\DoctrineQueryFilters\FiltersForm;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorFiltersForm extends FiltersForm implements PaginatorFormInterface, FilterFormInterface
{
    use PaginatorFormTrait {
        PaginatorFormTrait::configureOptions as paginatorConfigureOptions;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $this->paginatorConfigureOptions($resolver);
    }
}