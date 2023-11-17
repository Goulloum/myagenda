<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact')]
    public function index(ContactRepository $contactRepository): Response
    {

        $contacts = $contactRepository->findAll();
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts
        ]);
    }

    #[Route('/contact/{id}', name: 'app_contact_show', requirements: ['id' => '\d+'])]
    public function show(ContactRepository $contactRepository, $id): Response
    {
        $contact = $contactRepository->find($id);
        return $this->render('contact/show.html.twig', [
            'controller_name' => 'ContactController',
            'contact' => $contact
        ]);
    }

    #[Route('/contact/delete/{id}', name: 'app_contact_delete')]
    public function delete(ContactRepository $contactRepository, EntityManagerInterface $entityManagerInterface,  $id): Response
    {
        $contact = $contactRepository->find($id);
        $entityManagerInterface->remove($contact);
        $entityManagerInterface->flush();
        $this->addFlash(
            'success',
            'Le contact à bien été supprimé !'
        );
        return $this->redirectToRoute('app_contact');
    }

    #[Route('/contact/edit/{id}', name: 'app_contact_edit')]
    public function edit(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManagerInterface,  $id): Response
    {
        $contact = $contactRepository->find($id);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($contact);
            $entityManagerInterface->flush();
            $this->addFlash(
                'success',
                'Le contact à bien été modifié !'
            );
            return $this->redirectToRoute('app_contact');
        }


        return $this->render('contact/edit.html.twig', [
            'controller_name' => 'ContactController',
            'contactForm' => $form->createView()
        ]);
    }

    #[Route('/contact/add', name: 'app_contact_add')]
    public function add(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($contact);
            $entityManagerInterface->flush();
            $this->addFlash(
                'success',
                'Le contact à bien été enregistré !'
            );
            return $this->redirectToRoute('app_contact');
        }


        return $this->render('contact/add.html.twig', [
            'controller_name' => 'ContactController',
            'contactForm' => $form->createView()
        ]);
    }
}
