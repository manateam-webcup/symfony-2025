<?php

namespace App\Controller;

use App\Entity\Ending;
use App\Entity\UserEndingInteraction;
use App\Repository\UserEndingInteractionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/like')]
class LikeController extends AbstractController
{
    #[Route('/{id}', name: 'app_like', methods: ['POST'])]
    public function like(
        Ending $ending, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserEndingInteractionRepository $interactionRepository
    ): Response {
        // Only allow authenticated users to like
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        // Check if the user already has an interaction with this ending
        $interaction = $interactionRepository->findUserInteraction($user->getId(), $ending->getId());
        
        if ($interaction) {
            // If the user already liked this ending, remove the like
            if ($interaction->getType() === 'like') {
                $entityManager->remove($interaction);
                $ending->decrementLikes();
                $message = 'Like removed';
            } else {
                // If the user disliked this ending, change to like
                $interaction->setType('like');
                $ending->decrementDislikes();
                $ending->incrementLikes();
                $message = 'Changed from dislike to like';
            }
        } else {
            // Create a new like interaction
            $interaction = new UserEndingInteraction();
            $interaction->setUser($user);
            $interaction->setEnding($ending);
            $interaction->setType('like');
            $entityManager->persist($interaction);
            $ending->incrementLikes();
            $message = 'Liked';
        }
        
        $entityManager->flush();
        
        // If it's an AJAX request, return JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => $message,
                'likes' => $ending->getLikes(),
                'dislikes' => $ending->getDislikes()
            ]);
        }
        
        // Otherwise redirect back to the ending page
        return $this->redirectToRoute('app_ending_show', ['id' => $ending->getId()]);
    }
    
    #[Route('/dislike/{id}', name: 'app_dislike', methods: ['POST'])]
    public function dislike(
        Ending $ending, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserEndingInteractionRepository $interactionRepository
    ): Response {
        // Only allow authenticated users to dislike
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        // Check if the user already has an interaction with this ending
        $interaction = $interactionRepository->findUserInteraction($user->getId(), $ending->getId());
        
        if ($interaction) {
            // If the user already disliked this ending, remove the dislike
            if ($interaction->getType() === 'dislike') {
                $entityManager->remove($interaction);
                $ending->decrementDislikes();
                $message = 'Dislike removed';
            } else {
                // If the user liked this ending, change to dislike
                $interaction->setType('dislike');
                $ending->decrementLikes();
                $ending->incrementDislikes();
                $message = 'Changed from like to dislike';
            }
        } else {
            // Create a new dislike interaction
            $interaction = new UserEndingInteraction();
            $interaction->setUser($user);
            $interaction->setEnding($ending);
            $interaction->setType('dislike');
            $entityManager->persist($interaction);
            $ending->incrementDislikes();
            $message = 'Disliked';
        }
        
        $entityManager->flush();
        
        // If it's an AJAX request, return JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => $message,
                'likes' => $ending->getLikes(),
                'dislikes' => $ending->getDislikes()
            ]);
        }
        
        // Otherwise redirect back to the ending page
        return $this->redirectToRoute('app_ending_show', ['id' => $ending->getId()]);
    }
    
    #[Route('/status/{id}', name: 'app_like_status', methods: ['GET'])]
    public function status(
        Ending $ending, 
        UserEndingInteractionRepository $interactionRepository
    ): JsonResponse {
        // Get the current user's interaction status with this ending
        $status = 'none';
        
        if ($this->getUser()) {
            $interaction = $interactionRepository->findUserInteraction($this->getUser()->getId(), $ending->getId());
            if ($interaction) {
                $status = $interaction->getType();
            }
        }
        
        return new JsonResponse([
            'status' => $status,
            'likes' => $ending->getLikes(),
            'dislikes' => $ending->getDislikes()
        ]);
    }
}