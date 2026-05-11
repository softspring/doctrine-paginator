<?php

namespace Softspring\Component\DoctrinePaginator\Tests\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Softspring\Component\DoctrinePaginator\Paginator;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

class PaginatorFormTest extends TestCase
{
    public function testPaginatorFormDefaultsAndFields(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('createQueryBuilder')->with('s')->willReturn($queryBuilder);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->with(stdClass::class)->willReturn($repository);

        $formType = new class($em) extends PaginatorForm {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                parent::buildForm($builder, $options);
                $builder->add('search', TextType::class, [
                    'property_path' => '[name__like]',
                ]);
            }
        };

        $resolver = new OptionsResolver();
        $formType->configureOptions($resolver);
        $options = $resolver->resolve([
            'class' => stdClass::class,
            'rpp_valid_values' => [10, 25],
            'rpp_default_value' => 10,
            'order_valid_fields' => ['id', 'name'],
        ]);

        self::assertSame('10', $options['rpp_default_value']);
        self::assertSame(['10', '25'], $options['rpp_valid_values']);
        self::assertSame($queryBuilder, $options['query_builder']);

        $factory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new PreloadedExtension([$formType], []))
            ->getFormFactory();
        $form = $factory->createBuilder($formType::class, null, [
            'class' => stdClass::class,
            'em' => $em,
            'query_builder' => $queryBuilder,
            'rpp_valid_values' => [10, 25],
            'rpp_default_value' => 10,
            'order_valid_fields' => ['id', 'name'],
            'order_default_value' => 'id',
        ])->getForm();

        self::assertTrue($form->has('rpp'));
        self::assertTrue($form->has('order'));
        self::assertTrue($form->has('sort'));
    }

    public function testProcessPaginatedFilterFormNormalizesRequestData(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $formType = new class($this->createMock(EntityManagerInterface::class)) extends PaginatorForm {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                parent::buildForm($builder, $options);
                $builder->add('search', TextType::class, [
                    'property_path' => '[name__like]',
                ]);
            }
        };

        $factory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new PreloadedExtension([$formType], []))
            ->getFormFactory();
        $form = $factory->createBuilder($formType::class, null, [
            'class' => stdClass::class,
            'query_builder' => $queryBuilder,
            'rpp_valid_values' => [10, 25],
            'rpp_default_value' => 10,
            'order_valid_fields' => ['id', 'name'],
            'order_default_value' => 'id',
        ])->getForm();

        $form->submit([
            'search' => 'john',
            'rpp' => '25',
            'order' => 'name',
            'sort' => 'desc',
        ]);

        $request = Request::create('/admin/users?page=3');

        [$qb, $page, $rpp, $filters, $orderSort, $filtersMode] = Paginator::processPaginatedFilterForm($form, $request);

        self::assertSame($queryBuilder, $qb);
        self::assertSame('3', (string) $page);
        self::assertSame('25', $rpp);
        self::assertSame(['name__like' => 'john'], $filters);
        self::assertSame(['name' => 'desc'], $orderSort);
        self::assertSame(1, $filtersMode);
    }
}
