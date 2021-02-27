<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_USER for *every* controller method in this class.
 * @Route("/user")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/dashboard", name="user_dashboard")
     */
    public function userDashboard(): Response
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

        return $this->render('user/dashboard.html.twig', [
            'latest_article' => $latest_article,
        ]);
    }

    /**
     * @Route("/info", name="user_info")
     */
    public function userInfo(): Response
    {
        return $this->render('user/info.html.twig');
    }

    /**
     * @Route("/password", name="user_password")
     */
    public function userPassword(): Response
    {
        return $this->render('user/password.html.twig');
    }

    /**
     * @Route("/like", name="user_like")
     */
    public function userLike(): Response
    {
        return $this->render('user/like.html.twig');
    }

    /**
     * @Route("/share", name="user_share")
     */
    public function userShare(): Response
    {
        return $this->render('user/share.html.twig');
    }

    /**
     * @Route("/comment", name="user_comment")
     */
    public function adminDashboard(): Response
    {
        return $this->render('user/comment.html.twig');
    }
}
