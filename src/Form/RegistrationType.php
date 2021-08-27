<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class  ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre pseudo"
                ]
            ])
            ->add('email', EmailType::class ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre email"
                ]
            ])
            ->add('password', PasswordType::class ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre mot de passe"
                ]
            ])
            ->add('confirm_password', PasswordType::class ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez confirmer votre mot de passe"
                ]
            ])
            ->add("Valider", SubmitType::class)
            ->add('birthdate',BirthdayType::class ,[
                "widget"=>"choice",
                'years'=>range(1950, 2021),
                "format"=>"dd MM yyyy",
                "required"=>false,
                "label"=>false,
                "html5"=>false,
                "placeholder"=>['day'=>'jour', 'month'=>'Mois','year'=>'Année']

            ])
            ->add('adress',TextType::class  ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre adresse"
                ]
            ])
            ->add('cp',TextType::class  ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre code postal"
                ]
            ])
            ->add('city',TextType::class  ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre ville de résidence"
                ]
            ])
            ->add('phone',TextType::class  ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir votre numéro de téléphone"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
