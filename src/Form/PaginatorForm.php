<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorForm extends AbstractType implements PaginatorFormInterface
{
    use PaginatorFormTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->paginatorConfigureOptions($resolver);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->paginatorBuildForm($builder, $options);
    }
}
