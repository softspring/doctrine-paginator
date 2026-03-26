<?php

namespace Softspring\Component\DoctrinePaginator\Tests\Collection;

use Doctrine\Common\Collections\ArrayCollection;
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

        self::assertSame(0, $collection->getPages());
        self::assertNull($collection->getFirstPage());
        self::assertNull($collection->getLastPage());
        self::assertNull($collection->getNextPage());
        self::assertNull($collection->getPrevPage());
        self::assertTrue($collection->isFirstPage());
        self::assertTrue($collection->isLastPage());
        self::assertSame([], $collection->collapsedPages());
    }

    public function testGeneratesPageAndSortUrls(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 2, 10, 30, ['name' => 'asc']);
        $request = Request::create('/admin/users?status=active&page=2&order=name&sort=asc');

        self::assertSame('/admin/users?status=active&page=5&order=name&sort=asc', $collection->getPageUrl($request, 5));
        self::assertSame('/admin/users?status=active&page=1&order=email&sort=asc', $collection->getSortUrl($request, 'email', 'asc'));
        self::assertSame('/admin/users?status=active&page=1&order=name&sort=desc', $collection->getSortToggleUrl($request, 'name'));
    }
}
