<?php

namespace App\Grid\CustomForm;

use App\Entity\CustomForm\FormSubmissionValues;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class FormSubmissionValuesGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_form_submission_values';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('id')
                    ->setLabel('app.ui.id')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('formSubmission.id')
                    ->setLabel('app.ui.submission_id')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('formSubmission.customForm.name')
                    ->setLabel('app.ui.form_name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('field.label')
                    ->setLabel('app.ui.field_label')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('field.fieldType')
                    ->setLabel('app.ui.field_type')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('value')
                    ->setLabel('app.ui.value')
            )
            ->addFilter(
                Filter::create('formSubmission', 'custom_form_submission_type')
                    ->setOptions(['multiple' => false])
                    ->setLabel('app.ui.form_submission')
            )
        ;
    }

    public function getResourceClass(): string
    {
        return FormSubmissionValues::class;
    }
}
