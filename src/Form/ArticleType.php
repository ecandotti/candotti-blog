<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Contenu',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'Status',
                'choices' => [
                    'Publié' => 'P',
                    'Non Publié' => 'NP',
                ]
            ])
            ->add('poster', SubmitType::class, [
                'label' => 'Poster',
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
