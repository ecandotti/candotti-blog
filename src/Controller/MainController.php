<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\NewsLetter;
use App\Form\ContactType;
use App\Form\NewsLetterType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([],['createAt' => 'asc']);

        $articles = $paginator->paginate(
            $articles, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('base.html.twig', compact('articles'));
    }

    /**
     * @Route("/", name="newsletter", methods={"POST"})
     */
    public function newsletter(Request $request)
    {
        $newsletter = new NewsLetter();

        $form = $this->createForm(NewsLetterType::class, $newsletter);
        $form->handleRequest($request);

        if (!empty($form->get('email')->getData()) && is_string($form->get('email')->getData())) {
            $newsletter->setEmail($form->get('email')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newsletter);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription à la newsletter réussis !');
            return $this->redirectToRoute('home');
        }

        $this->addFlash('error', 'Erreur lors de l\'inscription à la newsletter.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact_post(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé avec succès, la réponse se fera dans nos meilleurs délais.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact.html.twig', [
            'contact' => $contact,
            'contactForm' => $form->createView(),
        ]);
    }
}
