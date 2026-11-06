<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/photo')]
#[IsGranted('ROLE_BENEVOLE')]
final class PhotoController extends AbstractController
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    #[Route(name: 'app_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->enregistrerFichier($form, $photo)) {
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photo_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Photo $photo): Response
    {
        return $this->render('photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_photo_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        $ancienFichier = $photo->getNomFichier();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->enregistrerFichier($form, $photo)) {
            $entityManager->flush();
            if ($ancienFichier !== $photo->getNomFichier()) {
                $this->supprimerFichier($ancienFichier);
            }

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photo_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($photo);
                $entityManager->flush();
                $this->supprimerFichier($photo->getNomFichier());
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('error', 'Suppression impossible : cet élément est encore utilisé. Modifiez d’abord les éléments associés.');
            }
        }

        return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
    }

    private function enregistrerFichier(FormInterface $form, Photo $photo): bool
    {
        $fichier = $form->get('fichier')->getData();
        if (!$fichier instanceof UploadedFile) {
            return true;
        }

        $nomFichier = bin2hex(random_bytes(16)).'.'.$fichier->guessExtension();
        try {
            $fichier->move($this->getParameter('kernel.project_dir').'/public/uploads/photos', $nomFichier);
        } catch (FileException) {
            $form->get('fichier')->addError(new FormError('Impossible d’enregistrer la photo. Réessayez.'));

            return false;
        }

        $photo->setNomFichier($nomFichier);

        return true;
    }

    private function supprimerFichier(?string $nomFichier): void
    {
        if ($nomFichier && preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|gif|webp)$/D', $nomFichier)) {
            $this->filesystem->remove($this->getParameter('kernel.project_dir').'/public/uploads/photos/'.$nomFichier);
        }
    }
}
