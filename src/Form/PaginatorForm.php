<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Softspring\Component\DoctrineQueryFilters\FiltersForm;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class PaginatorForm extends FiltersForm implements PaginatorFormInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'page_field_name' => 'page',
            'rpp_field_name' => 'rpp',
            'rpp_valid_values' => [50],
            'rpp_default_value' => 50,
            'order_field_name' => 'order',
            'order_valid_fields' => ['id'],
            'order_default_value' => 'id',
            'order_direction_field_name' => 'sort',
            'order_direction_valid_fields' => ['asc', 'desc'],
            'order_direction_default_value' => 'asc',
        ]);

        $resolver->setNormalizer('rpp_valid_values', function (Options $options, $value) {
            foreach ($value as $i => $v) {
                $value[$i] = "$v";
            }

            return $value;
        });

        $resolver->setNormalizer('rpp_default_value', function (Options $options, $value) {
            return "$value";
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add($options['rpp_field_name'], HiddenType::class, [
            'mapped' => false,
            'constraints' => new Choice([], $options['rpp_valid_values']),
        ]);

        $builder->add($options['order_field_name'], HiddenType::class, [
            'mapped' => false,
            'constraints' => new Choice([], $options['order_valid_fields']),
        ]);

        $builder->add($options['order_direction_field_name'], HiddenType::class, [
            'mapped' => false,
            'constraints' => new Choice([], $options['order_direction_valid_fields']),
        ]);
    }
}
