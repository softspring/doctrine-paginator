# Paginated collection

The *Paginator* results are contained into *PaginatedColection* object. 

It's a decoration of the doctrine's Collection with some specific pagination data:

```php
new PaginatedCollection(new ArrayCollection($results), $currentPage, $rpp, $totalResults, $orderBy);
```

## Parameters

### results

**type**: Doctrine\Common\Collections\Collection

The results are required to be contained in a Doctrine Collection. 

### page

**type**: int **default**: null

The current page number.

### rpp

**type**: int **default**: 10

The result per page number.

### total

**type**: int **default**: null

Total number of elements

### orderBy

**type**: array|null **default**: null

Optional orderBy.

## Methods

As the *PaginatedCollection* is a decoration of Doctrine Collection it provides all of its methods and behaviours.

Also this *PaginatedCollection* provides the following methods:

### getPage()

**type**: int

Returns the current page number.

### getRpp()

**type**: int

Returns the results per page number.

### getTotal()

**type**: int

Returns the total number of elements.

### getPages()

**type**: int|null

Returns the total number of pages, calculated from total and rpp numbers.

### getFirstPage()

**type**: int|null

The first page number. It can be null if the collection is empty.

### getLastPage()

**type**: int|null

The last page number. It can be null if the collection is empty.

### getNextPage()

**type**: int|null

The next page number. It can be null if the collection is empty.

### getPrevPage()

**type**: int|null

The previous page number. It can be null if the collection is empty.

### isFirstPage()

**type**: bool

If current page is the first one. 

### isLastPage()

**type**: bool

If current page is the last one. 

### collapsedPages()

**type**: array

This method returns an array with a limited number of pages, by default 5 pages.

#### Arguments

##### $elements

**type**: int **default**: 5

The number of page numbers to be collapsed.

##### $alwaysIncludeFirstAndLast

**type**: bool **default**: false

If always include first and last page numbers.

#### Example

For example, if we have a collection with 1000 results and a rpp of 100, if we show all page links:

    [1] [2] [3] *[4]* [5] [6] [7] [8] [9] [10]

If we collapse this pages to 5 elements (and, for example, we are in the 4th page):

    ... [2] [3] *[4]* [5] [6] ...

But, if we collapse to 3 elements:

    ... [3] *[4]* [5] ...

You can set $alwaysIncludeFirstAndLast to true, and then:

    [1] ... *[4]* ... [10]

If the current page is the first one and we show 5 elements:

    *[1]* [2] [3] [4] ... [10]

If the current page is the last one and we show 5 elements:

    [1] ... [7] [8] [9] *[10]*

### isOrderedBy

**type**: bool

Indicates either the results are ordered by some field.

#### Arguments

##### $orderField

**type**: string

### isSortedBy

**type**: bool

Indicates either the results are ordered by some field and direction.

#### Arguments

##### $orderField

**type**: string

##### $sortDirection

**type**: string|null **default**: null

### getSortUrl

**type**: string

Returns the current URL changing the order field and sort direction.

#### Arguments

##### $request

**type**: Request

##### $orderField

**type**: string

##### $sortDirection

**type**: string 

##### $orderParameterName

**type**: string **default**: order

##### $sortParameterName

**type**: string **default**: sort

##### $pageParameterName

**type**: string **default**: page

##### $referenceType

**type**: int **default**: UrlGeneratorInterface::ABSOLUTE_PATH

### getSortToggleUrl

**type**: string

Returns the toogled URL changing the direction of sorting.

#### Arguments

##### $request

**type**: Request

##### $orderField

**type**: string

##### $orderParameterName

**type**: string **default**: order

##### $sortParameterName

**type**: string **default**: sort

##### $pageParameterName

**type**: string **default**: page

##### $referenceType

**type**: int **default**: UrlGeneratorInterface::ABSOLUTE_PATH


