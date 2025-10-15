<?php

namespace App\Form\Type\CustomForm;

use App\Entity\CustomForm\CustomFormSubmission;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class CustomFromSubmissionAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => CustomFormSubmission::class,
            'placeholder' => 'Type to search fora custom form submission',
            'choice_label' => 'id',
            'label' => 'app.ui.custom_form_submission',
            'searchable_fields' => ['id'],
            'query_builder' => function (Options $options) {
                return function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('o');

                    $excluded = $options['extra_options']['excluded'] ?? [];
                    if ([] !== $excluded) {
                        $qb->andWhere($qb->expr()->notIn('o.id', $excluded));
                    }

                    return $qb;
                };
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
