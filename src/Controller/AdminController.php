<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/manage", name="admin_manage_comment", methods={"GET"})
     */
    public function adminManageComment(request $request, PaginatorInterface $paginator): Response
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([],
            ['createAt' => 'asc']
        );

        $comments = $paginator->paginate(
            $comments, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('admin/manage-comment.html.twig', [
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
        } elseif ($status == 'R') {
            $comment->setStatus('R');
            $em->flush();
        } else {
            $this->addFlash('error', 'Erreur de l\'action du commentaire !');
            return $this->redirectToRoute('admin_manage_comment');
        }

        $this->addFlash('success', 'Message validé avec succès !');
        return $this->redirectToRoute('admin_manage_comment');
    }
}
