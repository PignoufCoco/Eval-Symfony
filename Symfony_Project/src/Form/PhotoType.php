<?php

namespace App\Form;

use App\Entity\Benevole;
use App\Entity\Oeuvre;
use App\Entity\Photo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePriseVue', null, ['label' => 'Date de prise de vue'])
            ->add('nomFichier', null, ['label' => 'Nom du fichier'])
            ->add('etatOeuvre', null, ['label' => 'État de l’œuvre'])
            ->add('oeuvre', EntityType::class, [
                'class' => Oeuvre::class,
                'choice_label' => 'titre',
                'label' => 'Œuvre',
            ])
            ->add('benevole', EntityType::class, [
                'class' => Benevole::class,
                'choice_label' => static fn (Benevole $benevole): string => $benevole->getPrenom().' '.$benevole->getNom(),
                'label' => 'Bénévole',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
