<?php

namespace App\Form;

use App\Entity\Benevole;
use App\Entity\Oeuvre;
use App\Entity\Photo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $nouvellePhoto = null === $options['data']?->getId();

        $builder
            ->add('datePriseVue', null, ['label' => 'Date de prise de vue'])
            ->add('fichier', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => $nouvellePhoto,
                'help' => $nouvellePhoto ? 'JPG, PNG, GIF ou WebP, 2 Mo maximum.' : 'Laissez vide pour conserver la photo actuelle. JPG, PNG, GIF ou WebP, 2 Mo maximum.',
                'attr' => ['accept' => 'image/jpeg,image/png,image/gif,image/webp'],
                'constraints' => [
                    new Assert\Image(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                        mimeTypesMessage: 'Choisissez une image JPG, PNG, GIF ou WebP.',
                        maxSizeMessage: 'La photo doit faire moins de 2 Mo.',
                    ),
                    ...($nouvellePhoto ? [new Assert\NotBlank(message: 'Choisissez une photo.')] : []),
                ],
            ])
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
