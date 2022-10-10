<?php

namespace Softspring\Component\DoctrinePaginator\Form;

use Symfony\Component\Form\AbstractType;

class PaginatorForm extends AbstractType implements PaginatorFormInterface
{
    use PaginatorFormTrait;
}