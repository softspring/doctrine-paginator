<?php

namespace Softspring\Component\DoctrinePaginator\Collection;

use Doctrine\Common\Collections\Collection;
use Softspring\Component\DoctrinePaginator\Utils\Collapser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginatedCollection implements Collection
{
    protected Collection $originalCollection;

    protected ?int $page;

    protected ?int $rpp;

    protected ?int $total;

    protected ?array $orderBy;

    public function __construct(Collection $originalCollection, ?int $page = null, ?int $rpp = 10, ?int $total = null, ?array $orderBy = null)
    {
        $this->originalCollection = $originalCollection;
        $this->page = $page;
        $this->rpp = $rpp;
        $this->total = $total;
        $this->orderBy = $orderBy;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getRpp(): ?int
    {
        return $this->rpp;
    }

    public function getTotal(): ?int
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

        return ceil($this->total / $this->rpp);
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

    /* ****************************************************************************
     * IMPLEMENT DECORATED METHODS
     * **************************************************************************** */

    public function add($element)
    {
        return $this->originalCollection->add($element);
    }

    public function clear()
    {
        return $this->originalCollection->clear();
    }

    public function remove($key)
    {
        return $this->originalCollection->remove($key);
    }

    public function removeElement($element): bool
    {
        return $this->originalCollection->removeElement($element);
    }

    public function set($key, $value)
    {
        return $this->originalCollection->set($key, $value);
    }

    public function filter(\Closure $p): Collection
    {
        return $this->originalCollection->filter($p);
    }

    public function partition(\Closure $p): array
    {
        return $this->originalCollection->partition($p);
    }

    public function getIterator()
    {
        return $this->originalCollection->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->originalCollection->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->originalCollection->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->originalCollection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->originalCollection->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->originalCollection->count();
    }

    public function contains($element): bool
    {
        return $this->originalCollection->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->originalCollection->isEmpty();
    }

    public function containsKey($key): bool
    {
        return $this->originalCollection->containsKey($key);
    }

    public function get($key)
    {
        return $this->originalCollection->get($key);
    }

    public function getKeys(): array
    {
        return $this->originalCollection->getKeys();
    }

    public function getValues(): array
    {
        return $this->originalCollection->getValues();
    }

    public function toArray(): array
    {
        return $this->originalCollection->toArray();
    }

    public function first()
    {
        return $this->originalCollection->first();
    }

    public function last()
    {
        return $this->originalCollection->last();
    }

    public function key()
    {
        return $this->originalCollection->key();
    }

    public function current()
    {
        return $this->originalCollection->current();
    }

    public function next()
    {
        return $this->originalCollection->next();
    }

    public function slice($offset, $length = null): array
    {
        return $this->originalCollection->slice($offset, $length);
    }

    public function exists(\Closure $p): bool
    {
        return $this->originalCollection->exists($p);
    }

    public function map(\Closure $func): Collection
    {
        return $this->originalCollection->map($func);
    }

    public function forAll(\Closure $p): bool
    {
        return $this->originalCollection->forAll($p);
    }

    public function indexOf($element)
    {
        return $this->originalCollection->indexOf($element);
    }
}
