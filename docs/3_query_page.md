# Query page

First of all you need to create a *QueryBuilder*:

```php
$qb = $em->getRepository(Person::class)->createQueryBuilder('p');
```

Then you can query a results page:

```php
$page = 1;
$rpp = 50;
$results = Paginator::queryPage($qb, $page, $rpp);
```

## Filtering and sorting data

### With QueryBuilder

As *Paginator* works with a *QueryBuilder*, you can filter as usual:

```php
$qb->andWhere('p.name LIKE :name')->setParameter('name', '%John%');
```

Also you can sort results:

```php
$qb->orderBy('p.name', 'ASC')
```

> *This is not the recommended way, because *Paginator* can conflict with this orderBy.*

### Doctrine query filters

The *Paginator* uses [*softspring/doctrine-query-filters*](https://github.com/softspring/doctrine-query-filters) package 
 to provide data filtering.

It's easy to filter them with:

```php
$filters = ['country' => 'FR'];
$results = Paginator::queryPage($qb, $page, $rpp, $filters);
```

and you can add some complex filters (see [doctrine-query-filters documentation]()):

```php
$filters = [
    'name__like' => 'John',
    'country__in' => ['ES', 'FR'],
];
$results = Paginator::queryPage($qb, $page, $rpp, $filters);
```

Sorting is such easy adding the *$orderBy* parameter:

```php
$orderBy = ['name' => 'asc'];
$results = Paginator::queryPage($qb, $page, $rpp, $filters, $orderBy);
```

Also is posible to configure the *filters mode* (see [doctrine-query-filters documentation]()) to work 
 inclusively or exclusively.

```php
$results = Paginator::queryPage($qb, $page, $rpp, $filters, $orderBy, Filters::MODE_ADD);
$results = Paginator::queryPage($qb, $page, $rpp, $filters, $orderBy, Filters::MODE_OR);
```
