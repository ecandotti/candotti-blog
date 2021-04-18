<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\FilterArticleType;
use App\Form\FilterCommentType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
        // Get 5 latest article DESC
        $latest_article = $this->getDoctrine()->getRepository(Article::class)->findBy([
            'user' => $this->getUser()
        ], [
            'createAt' => 'DESC',
        ],5);

        // Get Comment with Waiting status
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'status' => 'W'
        ]);

        // If comment exist, display message
        if($comment) {
            $this->addFlash('msg', 'Des commentaires sont à gérer.');
        }

        return $this->render('admin/dashboard.html.twig', [
            'latest_article' => $latest_article,
        ]);
    }

    /**
     * @Route("/manage/article/multiDelete", name="multi_del_article")
     */
    public function multiDeleteArticle(Request $request, EntityManagerInterface $em): Response
    {

        // Get request contain id_article
        $result = $request->request->all();
        // Check if var is not empty, else display message
        if (empty($result)) {
            $this->addFlash('error','Aucun article selectionné');
            return $this->redirectToRoute('admin_manage_article');
        }

        // Map $result and remove article by its ID
        foreach ($result as $key => $value) {
            $article = $em->getRepository(Article::class)->findOneBy([
                'id' => $key
            ]);
            $em->remove($article);
        }
        $em->flush();

        // Display successfull message
        $this->addFlash('success','Articles selectionnés supprimés');
        return $this->redirectToRoute('admin_manage_article');
    }

    /**
     * @Route("/manage/comment/multiMove", name="multi_move_comment")
     */
    public function multiDeleteCommentary(Request $request, EntityManagerInterface $em): Response
    {
        // Get request
        $result = $request->request->all();
        // Get which action and remove it to had only id_comment
        $action = $result['actionMass'];
        unset($result["actionMass"]);

        // Check if $result is not empty, else display message
        if (empty($result)) {
            $this->addFlash('error','Aucun commentaire selectionné');
            return $this->redirectToRoute('admin_manage_comment');
        }

        // Map $result and remove article by its ID
        foreach ($result as $key => $value) {
            $comment = $em->getRepository(Comment::class)->findOneBy([
                "article" => $key
            ]);
            $comment->setStatus($action);
        }
        $em->flush();

        // Display successfull message
        $this->addFlash('success','Commentaires selectionnés modifié !');
        return $this->redirectToRoute('admin_manage_comment');
    }

    /**
     * @Route("/manage/article", name="admin_manage_article")
     */
    public function adminManageArticle(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FilterArticleType::class);
        $form->handleRequest($request);
        if (!empty($form->get('nameFilter')->getData())) {
            $currentDate = new DateTime('now');
            $statut = $form->get('nameFilter')->getData();
            if ($statut == "P") {
                $dql = "SELECT a FROM App:Article a WHERE a.publishAt < :statut";
                $articles = $em->createQuery($dql);
                $articles->setParameter('statut', $currentDate);
            } else {
                $dql = "SELECT a FROM App:Article a WHERE a.publishAt > :statut";
                $articles = $em->createQuery($dql);
                $articles->setParameter('statut', $currentDate);
            }
        } else {
            $dql = "SELECT a FROM App:Article a";
            $articles = $em->createQuery($dql);
        }

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
        // Create FilterForm
        $form = $this->createForm(FilterCommentType::class);
        $form->handleRequest($request);

        // If nameFilter exist, use filterName in findBy parameter else use nothing
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
    public function comment($status, $commentId)
    {
        // EntityManagerInterface
        $em = $this->getDoctrine()->getManager();
        // Get specific comment by its ID
        $comment = $em->getRepository(Comment::class)->find($commentId);

        // If doesn't exist, throw an error
        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '.$commentId
            );
        }

        // Change status of comment V=Valid, R=Refuse
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
