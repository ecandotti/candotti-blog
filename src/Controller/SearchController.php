<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/admin-search", name="admin_search")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminSearch(Request $request, ArticleRepository $articleRepo, PaginatorInterface $paginator): Response
    {
        $getResult = $request->get('search');
        $articleResult = $articleRepo->searchAllArticle($getResult);

        $articleResult = $paginator->paginate(
            $articleResult, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('search/index.html.twig', [
            'searchValue' => $getResult,
            'articleResult' => $articleResult,
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, ArticleRepository $articleRepo, PaginatorInterface $paginator): Response
    {
        $getResult = $request->get('search');
        $articleResult = $articleRepo->searchVisible($getResult);

        $articleResult = $paginator->paginate(
            $articleResult, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('search/index.html.twig', [
            'searchValue' => $getResult,
            'articleResult' => $articleResult,
        ]);
    }
}
