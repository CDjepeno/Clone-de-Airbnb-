<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class, $this->getConfiguration("Prenom","Modifier votre prenom"))
            ->add('lastName',TextType::class, $this->getConfiguration("Nom","Modifier votre nom de famille"))
            ->add('email',EmailType::class, $this->getConfiguration("Email","Modifier votre email"))
            ->add('picture',UrlType::class, $this->getConfiguration("Image","Modifier votre avatar"))
            ->add('introduction',TextType::class, $this->getConfiguration("Introduction","Modifier votre introduction"))
            ->add('description',TextareaType::class, $this->getConfiguration("Description","Modifier votre description"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
