<?php

namespace App\Controller;

use App\Entity\Oeuvre;
use App\Form\OeuvreType;
use App\Repository\OeuvreRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/oeuvre')]
final class OeuvreController extends AbstractController
{
    #[Route(name: 'app_oeuvre_index', methods: ['GET'])]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_oeuvre_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BENEVOLE')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_oeuvre_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_BENEVOLE')]
    public function edit(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_BENEVOLE')]
    public function delete(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($oeuvre);
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('error', 'Suppression impossible : cet élément est encore utilisé. Modifiez d’abord les éléments associés.');
            }
        }

        return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show_oeuvre_user', name: 'app_oeuvre_show_oeuvre_user', methods: ['GET'])]
    public function show_oeuvre_user(): Response
    {
        return $this->redirectToRoute('app_oeuvre_index');
    }
}
