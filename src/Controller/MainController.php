<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Like;
use App\Entity\NewsLetter;
use App\Entity\Share;
use App\Form\ContactType;
use App\Form\NewsLetterType;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([
            'status' => 'P'
        ],['createAt' => 'DESC']);

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Message envoyé avec succès, la réponse se fera dans nos meilleurs délais.');
        }

        return $this->render('contact.html.twig', [
            'contact' => $contact,
            'contactForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/likes/{id}", name="add_likes")
     */
    public function addLikes($id, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

        $user = $this->getUser();

        $alreadyLike = $em->getRepository(Like::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

        if ($alreadyLike) {
            $this->addFlash('error', 'Vous avez déjà cet article en favoris !');
            return $this->redirectToRoute('home');
        } else {
            $like = new Like();
            $like->setArticle($article);
            $like->setUser($user);
            $like->setCreateAt(new DateTime());
            $em->persist($like);
            $em->flush();

            $this->addFlash('success', 'Article ajouté aux favoris !');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/likes/remove/{id}", name="del_likes")
     */
    public function removeLikes($id, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

        $user = $this->getUser();

        $alreadyLike = $em->getRepository(Like::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

        if ($alreadyLike) {
            $em->remove($alreadyLike);
            $em->flush();

            $this->addFlash('success', 'Article retiré de vos favoris !');
            return $this->redirectToRoute('home');
        } else {
            $this->addFlash('error', 'Cet article n\'est pas dans vos favoris !');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/shares/add/{id}", name="add_shares")
     */
    public function addShares($id, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

        $user = $this->getUser();

        $alreadyShare = $em->getRepository(Share::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

        if ($alreadyShare) {
            $this->addFlash('error', 'Vous avez déjà partagé cet article !');
            return $this->redirectToRoute('home');
        } else {
            $share = new Share();
            $share->setArticle($article);
            $share->setUser($user);
            $share->setCreateAt(new DateTime());
            $em->persist($share);
            $em->flush();

            $this->addFlash('success', 'Article partagé avec succès !');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/shares/remove/{id}", name="del_shares")
     */
    public function removeShares($id, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

        $user = $this->getUser();

        $alreadyShare = $em->getRepository(Share::class)->findOneBy([
            'user' => $user,
            'article' => $article
        ]);

        if ($alreadyShare) {
            $em->remove($alreadyShare);
            $em->flush();

            $this->addFlash('success', 'Article retiré de vos partages !');
            return $this->redirectToRoute('home');
        } else {
            $this->addFlash('error', 'Vous n\'avez pas partagé cet article !');
            return $this->redirectToRoute('home');
        }
    }
}
