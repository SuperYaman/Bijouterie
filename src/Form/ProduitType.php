<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Matiere;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        if($options['ajouter'])
        {
            $builder
                ->add('titre', TextType::class, [
                    "label" => "Titre du produit",
                    "required" => false,
                    "attr" => [
                        "class" => "inputExemple"
                    ],
                    // "constraints" => [
                    //     new NotBlank([
                    //         "message" => "Veuillez renseigner un titre !!!"
                    //     ]),
                    //     new Length([
                    //         "min" => 5,
                    //         "max" => 10,
                    //         "minMessage" => "5 caractères min",
                    //         "maxMessage" => "10 caractères max"
                    //     ])
                    // ]
                ])


                
                ->add('prix', MoneyType::class, [
                    "currency" => "EUR",
                    // 'money_pattern' => 
                    "label" => "Prix du produit",
                    "required" => false,
                    "attr" => [
                        "placeholder" => "saisir le prix",
                        "style" => "background-color:aqua"
                    ]
                ])


                ->add('image', FileType::class, [
                    "required" => false
                ])
    
                // ->add("Ajouter", SubmitType::class)
                ->add('matieres', EntityType::class, [
                    "class"=>Matiere::class,
                    "choice_label"=>"name",
                    "multiple"=>true,
                    "attr"=>[
                        "class"=>"select2"
                    ]

                ])
                ->add('category', EntityType::class, [
                    "class"=>Category::class,
                    "choice_label"=>"name",

                ])
            
            ;
        }
        elseif($options['modifier'])
        {
            $builder
                ->add('titre', TextType::class, [
                    "label" => "Titre du produit",
                    "required" => false,
                    "attr" => [
                        "class" => "inputExemple"
                    ],
                    // "constraints" => [
                    //     new NotBlank([
                    //         "message" => "Veuillez renseigner un titre !!!"
                    //     ]),
                    //     new Length([
                    //         "min" => 5,
                    //         "max" => 10,
                    //         "minMessage" => "5 caractères min",
                    //         "maxMessage" => "10 caractères max"
                    //     ])
                    // ]
                ])


                
                ->add('prix', MoneyType::class, [
                    "currency" => "EUR",
                    // 'money_pattern' => 
                    "label" => "Prix du produit",
                    "required" => false,
                    "attr" => [
                        "placeholder" => "saisir le prix",
                        "style" => "background-color:aqua"
                    ]
                ])


                ->add('imageFile', FileType::class, [
                    "required" => false,
                    "mapped" => false
                ])
    
                // ->add("Ajouter", SubmitType::class)
                ->add('matieres', EntityType::class, [
                    "class"=>Matiere::class,
                    "choice_label"=>"name",
                    "multiple"=>true,
                    "attr"=>[
                        "class"=>"select2"
                    ]

                ])
                ->add('category', EntityType::class, [
                    "class"=>Category::class,
                    "choice_label"=>"name",

                ])
            
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'ajouter' => false,
            'modifier' => false
        ]);
    }
}
