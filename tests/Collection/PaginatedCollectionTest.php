<?php

declare(strict_types=1);

namespace Softspring\Component\DoctrinePaginator\Tests\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use LogicException;
use PHPUnit\Framework\TestCase;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Symfony\Component\HttpFoundation\Request;

class PaginatedCollectionTest extends TestCase
{
    public function testBasicPaginationMetadata(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a', 'b']), 2, 10, 35, ['name' => 'asc']);

        self::assertSame(2, $collection->getPage());
        self::assertSame(10, $collection->getRpp());
        self::assertSame(35, $collection->getTotal());
        self::assertSame(4, $collection->getPages());
        self::assertSame(1, $collection->getFirstPage());
        self::assertSame(4, $collection->getLastPage());
        self::assertSame(3, $collection->getNextPage());
        self::assertSame(1, $collection->getPrevPage());
        self::assertFalse($collection->isFirstPage());
        self::assertFalse($collection->isLastPage());
        self::assertTrue($collection->isOrderedBy('name'));
        self::assertTrue($collection->isSortedBy('name', 'asc'));
    }

    public function testEmptyPaginationMetadata(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(), 1, 10, 0);
        $request = Request::create('/admin/users?page=1');

        self::assertSame(0, $collection->getPages());
        self::assertNull($collection->getFirstPage());
        self::assertNull($collection->getLastPage());
        self::assertNull($collection->getNextPage());
        self::assertNull($collection->getPrevPage());
        self::assertNull($collection->getFirstPageUrl($request));
        self::assertNull($collection->getLastPageUrl($request));
        self::assertNull($collection->getNextPageUrl($request));
        self::assertNull($collection->getPrevPageUrl($request));
        self::assertTrue($collection->isFirstPage());
        self::assertTrue($collection->isLastPage());
        self::assertSame([], $collection->collapsedPages());
    }

    public function testFirstAndLastPageBoundaries(): void
    {
        $firstPage = new PaginatedCollection(new ArrayCollection(['a']), 1, 10, 30);
        $lastPage = new PaginatedCollection(new ArrayCollection(['c']), 3, 10, 30);
        $request = Request::create('/admin/users?status=active&page=1');

        self::assertTrue($firstPage->isFirstPage());
        self::assertFalse($firstPage->isLastPage());
        self::assertNull($firstPage->getPrevPage());
        self::assertSame(2, $firstPage->getNextPage());
        self::assertNull($firstPage->getPrevPageUrl($request));
        self::assertSame('/admin/users?status=active&page=2', $firstPage->getNextPageUrl($request));

        self::assertFalse($lastPage->isFirstPage());
        self::assertTrue($lastPage->isLastPage());
        self::assertSame(2, $lastPage->getPrevPage());
        self::assertNull($lastPage->getNextPage());
        self::assertSame('/admin/users?status=active&page=1', $lastPage->getFirstPageUrl($request));
        self::assertSame('/admin/users?status=active&page=3', $lastPage->getLastPageUrl($request));
        self::assertSame('/admin/users?status=active&page=2', $lastPage->getPrevPageUrl($request));
        self::assertNull($lastPage->getNextPageUrl($request));
    }

    public function testGetPagesRequiresResultsPerPage(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 1, 0, 1);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Rpp was not set');

        $collection->getPages();
    }

    public function testGeneratesPageAndSortUrls(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 2, 10, 30, ['name' => 'asc']);
        $request = Request::create('/admin/users?status=active&page=2&order=name&sort=asc');

        self::assertSame('/admin/users?status=active&page=5&order=name&sort=asc', $collection->getPageUrl($request, 5));
        self::assertSame('/admin/users?status=active&page=1&order=email&sort=asc', $collection->getSortUrl($request, 'email', 'asc'));
        self::assertSame('/admin/users?status=active&page=1&order=name&sort=desc', $collection->getSortToggleUrl($request, 'name'));
        self::assertSame('/admin/users?status=active&page=1&order=email&sort=asc', $collection->getSortToggleUrl($request, 'email'));
        self::assertFalse($collection->isOrderedBy('email'));
        self::assertFalse($collection->isSortedBy('name', 'desc'));
    }
}
