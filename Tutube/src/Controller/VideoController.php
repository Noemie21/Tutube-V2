<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/video')]
class VideoController extends AbstractController
{
    #[Route('/', name: 'video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'video_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $video = new Video();
        $video->setAuthor($this->getUser());
        $video->setViews(0);
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setSubstrLink(substr($video->getLink(), 32));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'video_show', methods: ['GET','POST'])]
    public function show(Video $video, Request $request): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setVideo($video);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('default', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('video/show.html.twig', [
            'video' => $video,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'video_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Video $video): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/addView', name: 'video_addview', methods: ['GET','POST'])]
    public function addView(Request $request, Video $video): Response
    {
        $video->setViews($video->getViews() + 1);
        $user = $video->getAuthor();
        $user->setTotalViews($user->getTotalViews() + 1);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
    }

    #[Route('/{id}', name: 'video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('video_index', [], Response::HTTP_SEE_OTHER);
    }
}
