<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $readTime = $form->getData()->getReadTime();
            
            // ReadTime function
            if ($readTime == NULL) {
                $len_content = strlen($form->getData()->getContent());
                $article->setReadTime(round($len_content / 60));
            }
            
            $article->setCreateAt(new DateTime());
            $article->setUser($this->getUser());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('home');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET", "POST"})
     */
    public function show(Article $article, Request $request)
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'article' => $article,
            'status' => "V"
        ],['createAt' => 'desc']);

        $latest_article = $this->getDoctrine()->getRepository(Article::class)->findBy([], [
            'createAt' => 'desc',
        ],5);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setStatus('W');
            $comment->setUser($this->getUser());
            $comment->setCreateAt(new DateTime('now'));

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($comment);
            $doctrine->flush();

            $this->addFlash('success', 'Commentaire posté avec succès. Cependant le commentaire doit être approuvé par l’administrateur pour être visible');
            return $this->render('article/show.html.twig', [
                'form' => $form->createView(),
                'article' => $article,
                'comments' => $comments,
                'latest_article' => $latest_article
            ]);
        }

        return $this->render('article/show.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'comments' => $comments,
            'latest_article' => $latest_article
        ]);
    }

    /**
     * Require ROLE_ADMIN for only this controller method.
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Article $article, $id): Response
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'article' => $article,
        ],['createAt' => 'desc']);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Article édité avec succès');
            return $this->redirectToRoute('article_show', [ 'id' => $id ]);
        }

        return $this->render('article/edit.html.twig', [
            'comments' => $comments,
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Require ROLE_ADMIN for only this controller method.
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }
        
        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('home');
    }
}
