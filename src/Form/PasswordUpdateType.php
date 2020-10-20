<?php

namespace App\Form;

use App\Entity\PasswordUpdate;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword',PasswordType::class, $this->getConfiguration("Mot de passe actuel","Tapez votre mot de passe actuel..."))
            ->add('newPassword',PasswordType::class, $this->getConfiguration("Nouveau mot de passe","Tapez votre nouveau mot de passe..."))
            ->add('confirmPassword',PasswordType::class, $this->getConfiguration("Confirmer nouveau mot de passe","Re-tapez votre nouveau mot de passe..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
