<?php

declare(strict_types=1);

namespace Softspring\Component\DoctrinePaginator\Tests\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;
use Softspring\Component\DoctrinePaginator\Utils\Collapser;

class CollapserTest extends TestCase
{
    public function testReturnsEmptyArrayForEmptyPagination(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(), 1, 10, 0);

        self::assertSame([], Collapser::collapse($collection));
    }

    public function testCollapsesAroundCurrentPage(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 5, 10, 120);

        self::assertSame([null, 4, 5, 6, null], Collapser::collapse($collection, 5, false));
        self::assertSame([1, null, 5, null, 12], Collapser::collapse($collection, 5, true));
    }

    public function testReturnsAllPagesWhenPageCountFitsRequestedElements(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 2, 10, 30);

        self::assertSame([1, 2, 3], Collapser::collapse($collection, 5));
    }

    public function testReturnsAllPagesWhenRequestedElementsIsLessThanMinimumCollapseSize(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 5, 10, 120);

        self::assertSame(range(1, 12), Collapser::collapse($collection, 4));
    }

    public function testCollapsesNearFirstPage(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 1, 10, 120);

        self::assertSame([1, 2, 3, 4, null], Collapser::collapse($collection, 5, false));
        self::assertSame([1, 2, 3, null, 12], Collapser::collapse($collection, 5, true));
    }

    public function testCollapsesNearLastPage(): void
    {
        $collection = new PaginatedCollection(new ArrayCollection(['a']), 12, 10, 120);

        self::assertSame([null, 9, 10, 11, 12], Collapser::collapse($collection, 5, false));
        self::assertSame([1, null, 10, 11, 12], Collapser::collapse($collection, 5, true));
    }
}
