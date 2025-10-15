<?php

namespace App\Grid\Filter;

use App\Form\Type\CustomForm\CustomFromSubmissionAutocompleteField;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\GridBundle\Doctrine\DataSourceInterface as DecoratedDataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;

class CustomFromSubmissionFilter implements ConfigurableFilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (!is_a($dataSource, DecoratedDataSourceInterface::class)) {
            throw new \Exception('Invalid data source');
        }

        $queryBuilder = $dataSource->getQueryBuilder();

        if (!is_a($queryBuilder, QueryBuilder::class)) {
            throw new \Exception('Invalid query builder');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->leftJoin(sprintf('%s.formSubmission', $rootAlias), 'formSubmissionFilter')
            ->andWhere('formSubmissionFilter.id = :formSubmission')
            ->setParameter('formSubmission', $data)
        ;
    }

    public static function getType(): string
    {
        return 'custom_form_submission_type';
    }

    public static function getFormType(): string
    {
        return CustomFromSubmissionAutocompleteField::class;
    }
}
