<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\VideoRepository;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default')]
    public function index(VideoRepository $videoRepository): Response
    {
        //dd($this->getUser());
        return $this->render('default/index.html.twig', [
            'user' => $this->getUser(),
            'videos' => $videoRepository->findBy(
                array(),
                ['publicationDate' => 'DESC'],
                20,
                0
            ),
        ]);
    }
}
