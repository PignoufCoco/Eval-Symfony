<?php

namespace App\Controller;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Repository\TechniqueRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/technique')]
#[IsGranted('ROLE_BENEVOLE')]
final class TechniqueController extends AbstractController
{
    #[Route(name: 'app_technique_index', methods: ['GET'])]
    public function index(TechniqueRepository $techniqueRepository): Response
    {
        return $this->render('technique/index.html.twig', [
            'techniques' => $techniqueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_technique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $technique = new Technique();
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($technique);
            $entityManager->flush();

            return $this->redirectToRoute('app_technique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('technique/new.html.twig', [
            'technique' => $technique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_technique_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Technique $technique): Response
    {
        return $this->render('technique/show.html.twig', [
            'technique' => $technique,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_technique_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Technique $technique, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_technique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('technique/edit.html.twig', [
            'technique' => $technique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_technique_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Technique $technique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$technique->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($technique);
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('error', 'Suppression impossible : cet élément est encore utilisé. Modifiez d’abord les éléments associés.');
            }
        }

        return $this->redirectToRoute('app_technique_index', [], Response::HTTP_SEE_OTHER);
    }
}
