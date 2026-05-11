<?php

declare(strict_types=1);

namespace Softspring\Component\DoctrinePaginator\Tests;

use PHPUnit\Framework\TestCase;
use Softspring\Component\DoctrinePaginator\Exception\InvalidFormTypeException;
use Softspring\Component\DoctrinePaginator\Paginator;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;

class PaginatorTest extends TestCase
{
    public function testProcessPaginatedFilterFormRejectsInvalidFormTypes(): void
    {
        $factory = Forms::createFormFactory();
        $form = $factory->createBuilder()->getForm();

        $this->expectException(InvalidFormTypeException::class);

        Paginator::processPaginatedFilterForm($form, Request::create('/admin/list'));
    }
}
