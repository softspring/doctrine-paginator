# Concepts

This *paginator* is the evolution of the old [*javihgil/doctrine-pagination-bundle*](https://github.com/javihgil/doctrine-pagination-bundle) package.

It provides doctrine pagination features, useful to query pages and to show them in views.

## Getting started to query

The main advantage with this paginator is that uses provided *QueryBuilder*s to generate pagination, instead of 
 the need of extending some specific repository:

```php
Paginator::queryPage($qb, $page);
```

It can be used in a third party bundle without entity or doctrine configuration change requirements.

Continue reading how to [query pages](3_query_page.md).

## Managing results

The *Paginator* always returns a *PaginatedCollection* object, that contains the query results and provides
 some pagination useful data.

More details can be read at [4. paginated collections](4_paginated_collection.md).

