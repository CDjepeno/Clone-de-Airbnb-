<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class, $this->getConfiguration("Prenom","Tapez votre prenom..."))
            ->add('lastName',TextType::class, $this->getConfiguration("Nom","Tapez votre nom..."))
            ->add('email',EmailType::class, $this->getConfiguration("Email","Tapez votre email..."))
            ->add('picture',UrlType::class, $this->getConfiguration("Image","Tapez l'url de votre avatar..."))
            ->add('hash',PasswordType::class, $this->getConfiguration("Mot de passe","Tapez votre mot de passe"))
            ->add("passwordConfirm",PasswordType::class, $this->getConfiguration("Confirmer votre mot de passe","Veuillez confirmer votre mot de passe"))
            ->add('introduction',TextType::class, $this->getConfiguration("Introduction","Donnez une description global de l'annonce..."))
            ->add('description',TextareaType::class, $this->getConfiguration("Description détaillée","Tapez une description détaillée..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
