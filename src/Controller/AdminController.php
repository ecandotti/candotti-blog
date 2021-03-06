<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\FilterArticleType;
use App\Form\FilterCommentType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function adminManageArticle(request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FilterArticleType::class);
        $form->handleRequest($request);

        if (!empty($form->get('nameFilter')->getData())) {
            $statut = $form->get('nameFilter')->getData();
            $dql = "SELECT a FROM App:Article a WHERE a.status = :statut";
            $articles = $em->createQuery($dql);
            $articles->setParameter('statut', (string) $statut);
        } else {
            $dql = "SELECT a FROM App:Article a";
            $articles = $em->createQuery($dql);
        }
        // dd($articles);

        $articles = $paginator->paginate(
            $articles, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('admin/manage-articles.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/manage/comment", name="admin_manage_comment", methods={"GET","POST"})
     */
    public function adminManageComment(request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {   
        $form = $this->createForm(FilterCommentType::class);
        $form->handleRequest($request);

        if (!empty($form->get('nameFilter')->getData())) {
            $statut = $form->get('nameFilter')->getData();
            $comments = $em->getRepository(Comment::class)->findBy([
                'status' => $statut
            ]);
        } else {
            $comments = $em->getRepository(Comment::class)->findBy([]);
        }
        
        $comments = $paginator->paginate(
            $comments, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );
        
        return $this->render('admin/manage-comments.html.twig', [
            'form' => $form->createView(),
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
