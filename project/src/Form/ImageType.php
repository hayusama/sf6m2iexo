<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //DATA_CLASS NULL EVITE LE MAPPING DE IMAGE SUR LE CHAMPS CHEMIN PAR DEFAUT DATA_CLASS = IMAGE
        $builder
            ->add('chemin', FileType::class, ["label"=>"Votre fichier", "data_class" => null])
            ->add('alt', TextType::class)
            ->add('published', CheckboxType::class, [
                                                        'label' => "Publier l'image?",
                                                        'attr' => ["checked" => "checked"]
                                                    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
