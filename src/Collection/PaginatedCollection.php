<?php

namespace Softspring\Component\DoctrinePaginator\Collection;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Softspring\Component\DoctrinePaginator\Utils\Collapser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginatedCollection implements Collection
{
    protected Collection $results;

    protected int $page;

    protected int $rpp;

    protected int $total;

    protected ?array $orderBy;

    public function __construct(Collection $results, int $page, int $rpp, int $total, ?array $orderBy = null)
    {
        $this->results = $results;
        $this->page = $page;
        $this->rpp = $rpp;
        $this->total = $total;
        $this->orderBy = $orderBy;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getRpp(): int
    {
        return $this->rpp;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPages(): int
    {
        if (!$this->getRpp()) {
            throw new \LogicException('Rpp was not set');
        }

        if (!$this->getTotal()) {
            return 0;
        }

        return (int) ceil($this->total / $this->rpp);
    }

    public function getFirstPage(): ?int
    {
        if (0 == $this->getPages()) {
            return null;
        }

        return 1;
    }

    public function getLastPage(): ?int
    {
        if (0 == $this->getPages()) {
            return null;
        }

        return $this->getPages();
    }

    public function getNextPage(): ?int
    {
        if (!$this->isLastPage()) {
            return $this->getPage() + 1;
        }

        return null;
    }

    public function getPrevPage(): ?int
    {
        if (!$this->isFirstPage()) {
            return $this->getPage() - 1;
        }

        return null;
    }

    public function isFirstPage(): bool
    {
        return 1 === $this->getPage();
    }

    public function isLastPage(): bool
    {
        return !$this->getPages() || $this->getPage() == $this->getPages();
    }

    public function collapsedPages(int $elements = 5, bool $alwaysIncludeFirstAndLast = false): array
    {
        return Collapser::collapse($this, $elements, $alwaysIncludeFirstAndLast);
    }

    /* ****************************************************************************
     * UTIL METHODS
     * **************************************************************************** */

    public function isSortedBy(string $orderField, ?string $sortDirection = null): bool
    {
        return $this->isOrderedBy($orderField) && $this->orderBy[$orderField] == $sortDirection;
    }

    public function isOrderedBy(string $orderField): bool
    {
        return isset($this->orderBy[$orderField]);
    }

    public function getSortToggleUrl(Request $request, string $orderField, string $orderParameterName = 'order', string $sortParameterName = 'sort', string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ($this->isOrderedBy($orderField)) {
            $inverseOrder = $this->isSortedBy($orderField, 'asc') ? 'desc' : 'asc';

            return $this->getSortUrl($request, $orderField, $inverseOrder, $orderParameterName, $sortParameterName, $pageParameterName, $referenceType);
        } else {
            return $this->getSortUrl($request, $orderField, 'asc', $orderParameterName, $sortParameterName, $pageParameterName, $referenceType);
        }
    }

    public function getSortUrl(Request $request, string $orderField, string $sortDirection, string $orderParameterName = 'order', string $sortParameterName = 'sort', string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $url = $request->getPathInfo();
        $query = $request->query->all();

        $query[$orderParameterName] = $orderField;
        $query[$sortParameterName] = $sortDirection;
        $query[$pageParameterName] = 1;

        return $url.'?'.http_build_query($query);
    }

    public function getPageUrl(Request $request, int $page, string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $url = $request->getPathInfo();
        $query = $request->query->all();
        $query[$pageParameterName] = $page;

        return $url.'?'.http_build_query($query);
    }

    public function getFirstPageUrl(Request $request, string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        return $this->getFirstPage() ? $this->getPageUrl($request, $this->getFirstPage(), $pageParameterName, $referenceType) : null;
    }

    public function getLastPageUrl(Request $request, string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        return $this->getLastPage() ? $this->getPageUrl($request, $this->getLastPage(), $pageParameterName, $referenceType) : null;
    }

    public function getNextPageUrl(Request $request, string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        return $this->getNextPage() ? $this->getPageUrl($request, $this->getNextPage(), $pageParameterName, $referenceType) : null;
    }

    public function getPrevPageUrl(Request $request, string $pageParameterName = 'page', int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        return $this->getPrevPage() ? $this->getPageUrl($request, $this->getPrevPage(), $pageParameterName, $referenceType) : null;
    }

    /* ****************************************************************************
     * IMPLEMENT DECORATED METHODS
     * **************************************************************************** */

    public function add($element)
    {
        $this->results->add($element);
    }

    public function clear()
    {
        $this->results->clear();
    }

    public function remove($key)
    {
        $this->results->remove($key);
    }

    public function removeElement($element): bool
    {
        return $this->results->removeElement($element);
    }

    public function set($key, $value)
    {
        $this->results->set($key, $value);
    }

    public function filter(\Closure $p): ReadableCollection
    {
        return $this->results->filter($p);
    }

    public function partition(\Closure $p): array
    {
        return $this->results->partition($p);
    }

    public function getIterator(): \Traversable
    {
        return $this->results->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->results->offsetExists($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->results->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->results->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->results->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->results->count();
    }

    public function contains($element): bool
    {
        return $this->results->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->results->isEmpty();
    }

    public function containsKey($key): bool
    {
        return $this->results->containsKey($key);
    }

    public function get($key)
    {
        return $this->results->get($key);
    }

    public function getKeys(): array
    {
        return $this->results->getKeys();
    }

    public function getValues(): array
    {
        return $this->results->getValues();
    }

    public function toArray(): array
    {
        return $this->results->toArray();
    }

    public function first()
    {
        return $this->results->first();
    }

    public function last()
    {
        return $this->results->last();
    }

    public function key()
    {
        return $this->results->key();
    }

    public function current()
    {
        return $this->results->current();
    }

    public function next()
    {
        return $this->results->next();
    }

    public function slice($offset, $length = null): array
    {
        return $this->results->slice($offset, $length);
    }

    public function exists(\Closure $p): bool
    {
        return $this->results->exists($p);
    }

    public function map(\Closure $func): ReadableCollection
    {
        return $this->results->map($func);
    }

    public function forAll(\Closure $p): bool
    {
        return $this->results->forAll($p);
    }

    public function indexOf($element)
    {
        return $this->results->indexOf($element);
    }

    /**
     * @throws \Exception
     */
    public function findFirst(\Closure $p)
    {
        if (!method_exists(ReadableCollection::class, 'findFirst')) {
            throw new \Exception('This findFirst method is only available with doctrine/collections >= 2.0, witch is only compatible with PHP >= 8.1');
        }

        return $this->results->findFirst($p);
    }

    /**
     * @throws \Exception
     */
    public function reduce(\Closure $func, mixed $initial = null)
    {
        if (!method_exists(ReadableCollection::class, 'reduce')) {
            throw new \Exception('This reduce method is only available with doctrine/collections >= 2.0, witch is only compatible with PHP >= 8.1');
        }

        return $this->results->reduce($func, $initial);
    }
}
