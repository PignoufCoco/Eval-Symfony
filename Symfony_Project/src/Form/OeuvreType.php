<?php

namespace App\Form;

use App\Entity\Artiste;
use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Technique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OeuvreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('dateCreation')
            ->add('description')
            ->add('dateDestruction')
            ->add('artistes', EntityType::class, [
                'class' => Artiste::class,
                'choice_label' => 'pseudonyme',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('lieux', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('technique', EntityType::class, [
                'class' => Technique::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('oeuvreRecouverte', EntityType::class, [
                'class' => Oeuvre::class,
                'choice_label' => 'titre',
                'required' => false,
            ])
            ->add('recouvertePar', EntityType::class, [
                'class' => Oeuvre::class,
                'choice_label' => 'titre',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
        ]);
    }
}
