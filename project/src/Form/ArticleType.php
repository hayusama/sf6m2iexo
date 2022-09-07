<?php

namespace App\Form;

use App\Entity\Article;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', ImageType::class)
            ->add('title', TextType::class, ["label"=>"Votre titre :"])
            ->add('content', TextareaType::class, ["label"=> "Le contenu de votre article :"])
            ->add('published', CheckboxType::class, ['label' => "Publier l'article"])
            ->add('sauvegarder', SubmitType::class, ["attr" => ['class'=>'btn-danger']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
