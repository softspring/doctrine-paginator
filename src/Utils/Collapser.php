<?php

namespace Softspring\Component\DoctrinePaginator\Utils;

use Softspring\Component\DoctrinePaginator\Collection\PaginatedCollection;

class Collapser
{
    public static function collapse(PaginatedCollection $collection, int $elements = 5, bool $alwaysIncludeFirstAndLast = false): array
    {
        $current = (int) $collection->getPage();
        $pages = $collection->getPages();
        $pagesArray = range(1, $pages);

        if ($elements < 5 || $pages <= $elements) {
            return $pagesArray;
        }

        // calculate pivot
        $start = $current - floor(($elements - 1) / 2);

        // fix extreme cases
        if ($start < 1) {
            $start = 1;
        }
        $end = $current + ceil(($elements - 1) / 2);
        if ($end > $pages) {
            $start -= $end - $pages;
        }

        // remove extra elements
        $pagesArray = array_slice($pagesArray, $start - 1, $elements);

        if ($alwaysIncludeFirstAndLast) {
            // add null at the beginning
            if (1 != $pagesArray[0]) {
                array_shift($pagesArray);
                array_unshift($pagesArray, 1);
                if (isset($pagesArray[1]) && 2 != $pagesArray[1]) {
                    $pagesArray[1] = null;
                }
            }
            // add null at the beginning
            if ($pagesArray[$elements - 1] != $pages) {
                array_pop($pagesArray);
                array_push($pagesArray, $pages);

                if (isset($pagesArray[$elements - 2]) && $pagesArray[$elements - 2] != $pages - 1) {
                    $pagesArray[$elements - 2] = null;
                }
            }
        } else {
            // add null at the beginning
            if (1 != $pagesArray[0]) {
                $pagesArray[0] = null;
            }
            // add null at the beginning
            if ($pagesArray[$elements - 1] != $pages) {
                $pagesArray[$elements - 1] = null;
            }
        }

        return $pagesArray;
    }
}
