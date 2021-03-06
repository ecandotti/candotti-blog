<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameFilter', ChoiceType::class, [
                'required' => true,
                'label' => 'Filtrer par',
                'choices' => [
                    ' ' => null,
                    'Publié' => 'P',
                    'Non Publié' => 'NP',
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Filtrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
