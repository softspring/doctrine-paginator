# Doctrine Paginator Features

Functional definition for `softspring/doctrine-paginator`.

This file defines the expected behavior and scope of the component.

## Purpose

- Paginate Doctrine query builders in a reusable way.
- Provide a small bridge between paginator-aware Symfony filter forms and Doctrine queries.
- Expose pagination metadata and pagination URLs through a dedicated collection object.

## Main Features

- Paginate a Doctrine `QueryBuilder` into a `PaginatedCollection`.
- Apply filters and sorting while paginating.
- Compute aggregate values on the same filtered query.
- Process paginator-related form data such as page, results per page, and ordering.
- Expose helper methods for pagination links, sorting links, and collapsed page lists.

## Expected Usage

- Use `Paginator::queryPage()` when pagination inputs are already normalized.
- Use `Paginator::queryPaginatedFilterForm()` when a Symfony form controls filters, sorting, and page state.
- Use `PaginatedCollection` in controllers, templates, or serializers that need both results and pagination metadata.
- Use `PaginatorForm` as a base type when a listing page needs GET-based pagination controls.

## Operational Expectations

- Invalid paginator form types should fail with an explicit exception.
- Invalid filter values should bubble up through the underlying query-filter component.
- Empty result sets should still produce a predictable pagination object.
- Pagination helpers should stay useful for both HTML listings and service-layer pagination flows.

## Current Limits

- The package focuses on Doctrine ORM query builders, not generic data sources.
- URL helpers derive links from the current request path and query string.
- The form layer is intentionally lightweight and expects the application to define its own actual filter fields.
