<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function adminDashboard(): Response
    {
        $latest_article = $this->getDoctrine()->getRepository(Article::class)->findBy([
            'user' => $this->getUser()
        ], [
            'createAt' => 'DESC',
        ],5);

        $comment = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'status' => 'W'
        ]);

        if($comment) {
            $this->addFlash('msg', 'Des commentaires sont à gérer.');
        }

        return $this->render('admin/dashboard.html.twig', [
            'latest_article' => $latest_article,
        ]);
    }

    /**
     * @Route("/manage/article", name="admin_manage_article")
     */
    public function adminManageArticle(request $request, PaginatorInterface $paginator): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([
            'user' => $this->getUser()
        ],
            ['createAt' => 'DESC']
        );

        $articles = $paginator->paginate(
            $articles, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('admin/manage-articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/manage/comment", name="admin_manage_comment", methods={"GET"})
     */
    public function adminManageComment(request $request, PaginatorInterface $paginator): Response
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([],
            ['createAt' => 'DESC']
        );

        $comments = $paginator->paginate(
            $comments, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('admin/manage-comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/manage?status={status}&commentId={commentId}", name="comment_action")
     */
    public function comment(Request $request, $status, $commentId)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '.$commentId
            );
        }
        if ($status == 'V') {
            $comment->setStatus('V');
            $em->flush();
            $this->addFlash('success', 'Message validé avec succès !');
        } elseif ($status == 'R') {
            $comment->setStatus('R');
            $em->flush();
            $this->addFlash('success', 'Message refusé avec succès !');
        } else {
            $this->addFlash('error', 'Erreur de l\'action du commentaire !');
            return $this->redirectToRoute('admin_manage_comment');
        }
        return $this->redirectToRoute('admin_manage_comment');
    }
}
