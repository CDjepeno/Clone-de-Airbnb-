<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class, $this->getConfiguration("Date d'arrivée","La date à laquelle vous comptez arriver"))
            ->add('endDate',TextType::class, $this->getConfiguration("Date de départ","La date à laquelle vous quitter les lieux"))
            ->add('comment',TextareaType::class, $this->getConfiguration(false,"Si vous avez un commentaire, n'hésitez pas à en faire part !",["required" => false]))
        ;
        $builder->get('startDate')
            ->addModelTransformer(new CallbackTransformer(
                function ($date) {
                    if($date === null) {
                        return '';
                    }
                    return $date->format('d/m/Y');
                },
                function ($frenchDate) {
                    if($frenchDate === null) {
                        throw new TransformationFailedException("Vous devez fournir une date");
                    }

                    $date = \DateTime::createFromFormat('d/m/Y',$frenchDate);

                    if($date === false) {
                        throw new TransformationFailedException("Le format n'est pas le bon");
                    }
                    return $date;
                }
            ))
        ;
        $builder->get('endDate')
            ->addModelTransformer(new CallbackTransformer(
                function ($date) {
                    if($date === null) {
                        return '';
                    }
                    return $date->format('d/m/Y');
                },
                function ($frenchDate) {
                    if($frenchDate === null) {
                        throw new TransformationFailedException("Vous devez fournir une date");
                    }

                    $date = \DateTime::createFromFormat('d/m/Y',$frenchDate);

                    if($date === false) {
                        throw new TransformationFailedException("Le format n'est pas le bon");
                    }
                    return $date;
                }
            ))
        ; 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            "validation_groups" => ["Default","front"],
            'csrf_protection' => false
        ]);
    }
}
