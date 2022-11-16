<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Softspring\Component\DoctrineQueryFilters\FilterFormInterface;
use Softspring\Component\DoctrineQueryFilters\FiltersForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorFiltersForm extends FiltersForm implements PaginatorFormInterface, FilterFormInterface
{
    use PaginatorFormTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $this->paginatorConfigureOptions($resolver);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $this->paginatorBuildForm($builder, $options);
    }
}