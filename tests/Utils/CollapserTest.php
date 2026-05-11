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
}
