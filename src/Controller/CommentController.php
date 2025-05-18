<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ending;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function add(Ending $ending, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Only allow authenticated users to comment
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $content = $request->request->get('content');
        
        if (empty($content)) {
            $this->addFlash('error', 'Comment cannot be empty.');
            return $this->redirectToRoute('app_ending_show', ['id' => $ending->getId()]);
        }
        
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setUser($this->getUser());
        $comment->setEnding($ending);
        
        $entityManager->persist($comment);
        $entityManager->flush();
        
        $this->addFlash('success', 'Your comment has been added.');
        
        return $this->redirectToRoute('app_ending_show', ['id' => $ending->getId()]);
    }
}