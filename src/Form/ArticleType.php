<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre',
            ])
            ->add('subtitle', TextType::class, [
                'required' => true,
                'label' => 'Sous Titre',
            ])
            ->add('readTime', NumberType::class, [
                'required' => false,
                'label' => 'Temps de lecture',
            ])
            ->add('content', CKEditorType::class, [
                'required' => true,
                'label' => 'Contenu',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Upload une image',
            ])
            ->add('category', ChoiceType::class, [
                'required' => true,
                'label' => 'Categorie',
                'choices' => [
                    'Humour' => 'humor',
                    'Fantaisie' => 'fancy',
                    'Politique' => 'politics',
                ]
            ])
            ->add('publishAt', DateTimeType::class, [
                'label' => 'Date de publication',
            ])
            ->add('isVisible', ChoiceType::class, [
                'required' => true,
                'label' => 'Status :',
                'choices' => [
                    'Visible' => true,
                    'Non visible' => false,
                ]
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
