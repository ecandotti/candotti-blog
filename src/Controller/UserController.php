<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Share;
use App\Entity\User;
use App\Form\ChangePwdType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $fifth_article_like = $this->getDoctrine()->getRepository(Like::class)->findBy([
            'user' => $this->getUser()
        ], [
            'createAt' => 'DESC',
        ],5);

        $fifth_article_share = $this->getDoctrine()->getRepository(Share::class)->findBy([
            'user' => $this->getUser()
        ], [
            'createAt' => 'DESC',
        ],5);

        $fifth_article_comment = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'user' => $this->getUser()
        ], [
            'createAt' => 'DESC',
        ],5);

        return $this->render('user/dashboard.html.twig', [
            'fifth_article_like' => $fifth_article_like,
            'fifth_article_share' => $fifth_article_share,
            'fifth_article_comment' => $fifth_article_comment,
        ]);
    }

    /**
     * @Route("/info", name="user_info")
     */
    public function userInfo(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Information édité avec succès');
            return $this->redirectToRoute('user_info');
        }

        return $this->render('user/info.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password", name="user_password")
     */
    public function userPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo): Response
    {
        $form = $this->createForm(ChangePwdType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            
            $formInfo = $form->getData();
            $old_pwd = $formInfo['oldPassword']; 
            $new_pwd = $formInfo['newPassword'];

            $checkPass = $passwordEncoder->isPasswordValid($user, $old_pwd);
            
            if($checkPass) {
                $userRepo->upgradePassword($user, $passwordEncoder->encodePassword($user, $new_pwd));
            } else {
                $this->addFlash('error', 'Mot de passe actuelle est erroné !');
                return $this->redirectToRoute('user_password');
            }

            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('user_password');
        }

        return $this->render('user/password.html.twig', [
            'passForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/like", name="user_like")
     */
    public function userLike(Request $request, PaginatorInterface $paginator): Response
    {
        $articles_like = $this->getDoctrine()->getRepository(Like::class)->findBy([
            'user' => $this->getUser()
        ]);

        $articles = $paginator->paginate(
            $articles_like, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('user/like.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/share", name="user_share")
     */
    public function userShare(Request $request, PaginatorInterface $paginator): Response
    {
        $articles_share = $this->getDoctrine()->getRepository(Share::class)->findBy([
            'user' => $this->getUser()
        ],['createAt' => 'DESC']);

        $articles = $paginator->paginate(
            $articles_share, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('user/share.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/comment", name="user_comment")
     */
    public function adminDashboard(Request $request, PaginatorInterface $paginator): Response
    {
        $articles_comment = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'user' => $this->getUser(),
            'status' => 'V'
        ],['createAt' => 'DESC']);

        $articles = $paginator->paginate(
            $articles_comment, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('user/comment.html.twig', [
            'articles' => $articles
        ]);
    }
}
