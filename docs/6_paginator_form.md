# Paginator form

This *Paginator* component also provides a **PaginatorForm** that includes everything together and ready to use.

In the common situation you should:

```php
public function list(Request $request): Response
{
    $page = $request->query->get('page', 1);
    $rpp = $request->query->get('rpp', 50);
    $order = $request->query->get('order', 'name');
    $sort = $request->query->get('sort', 'asc');
    
    $form = $this->createForm(UsersListFilterForm::class)->handleRequest($request);
    $filters = $form->isSubmitted() && $form->isValid() ? array_filter($form->getData()) : [];

    $qb = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u');
    $results = Paginator::queryPage($qb, $page, $rpp, $filters, [$order => $sort]);
    
    ...
}
```

So, you have to manage the pagination data, sorting fields, filter form and query with the Paginator.

All of this can be done using *PaginatorForm*.

```php
public function list(Request $request): Response
{
    $form = $this->createForm(UsersListFilterForm::class)->handleRequest($request);
    $results = Paginator::queryPaginatedFilterForm($form, $request);
    ...
}
    
```

The **PaginatorForm** contains all the required configuration, lets know it.

## Create a PaginatorForm

```php
<?php

namespace App\Form;

use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersListFilterForm extends PaginatorForm
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver); // THIS IS MANDATORY, IT INCLUDES ALL THE REQUIRED OPTIONS

        $resolver->setDefaults([
            'class' => User::class,
        ]);
    }
}
```

This form can be configured using the options described bellow.

## Options

### page_field_name

**type**: string **default**: 'page'

The page parameter name for the URLs·

### rpp_field_name

**type**: string **default**: 'rpp'

The rpp parameter name for the URLs·

### rpp_valid_values

**type**: int[] **default**: [50]

An array with a set of valid rpp valid values.

### rpp_default_value

**type**: int **default**: 50

The rpp default value (used if not set or invalid value).

### order_field_name

**type**: string **default**: 'order'

The order parameter name for the URLs·

### order_valid_fields

**type**: string[] **default**: ['id']

An array with a set of valid ordering fields.

### order_default_value

**type**: string **default**: 'id'

The order field default value (used if not set or invalid value).

### order_direction_field_name

**type**: string **default**: 'sort'

The order direction parameter name for the URLs·

### order_direction_valid_fields

**type**: string[] **default**: ['asc', 'desc']

An array with a set of valid order direction valid values.

### order_direction_default_value

**type**: string **default**: 'asc'

The order direction default value (used if not set or invalid value).

## FiltersForm inherited options

**PaginatorForm** is an extension of **Softspring\Component\DoctrineQueryFilters\FiltersForm**.

By default, the *FiltersForm* sets the following option values:

- csrf_protection: false
- method: 'GET'
- required: false
- attr: ['novalidate' => 'novalidate']
- allow_extra_fields: true

Also it privides some options, some of them very important for us.


### query_builder_mode

**type**: int **default**: Filters::MODE_AND

### em

**type**: EntityManagerInterface

The Doctrine Entity Manager. The default behaviour is to use the one provided to the constructor, but a custom one can be set here. 

### class

**type**: string|null **default**: null

This is the name of the class to be queried. If no *query_builder* is provided, it's used to generate one.

### query_builder

**type**: QueryBuilder|callable|null **default**: null

You can specify a QueryBuilder, a callable witch creates one or leave it as null. In this case a new *class* query builder
 is generated.

## Full common example

```php
<?php

namespace App\Form;

use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersListFilterForm extends PaginatorForm
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver); // THIS IS MANDATORY, IT INCLUDES ALL THE REQUIRED OPTIONS

        $resolver->setDefaults([
            'class' => User::class,
            'rpp_valid_values' => [20, 50, 100],
            'rpp_default_value' => 20,
            'order_valid_fields' => ['id', 'name', 'surname', 'email'],
            'order_default_value' => 'name',
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options); // THIS IS MANDATORY, IT INCLUDES ALL THE REQUIRED FIELDS

        $builder->add('name', TextType::class, [
            'property_path' => '[name__like]',
        ]);

        $builder->add('surname', TextType::class, [
            'property_path' => '[surname__like]',
        ]);

        $builder->add('email', TextType::class, [
            'property_path' => '[email__like]',
        ]);

        $builder->add('text', TextType::class, [
            'property_path' => '[name__like___or___surname__like___or___email__like]',
        ]);
    }
}
```