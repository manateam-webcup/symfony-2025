<?php

namespace App\Controller;

use App\Entity\Ending;
use App\Form\EndingType;
use App\Repository\CommentRepository;
use App\Repository\EndingRepository;
use App\Repository\UserEndingInteractionRepository;
use App\Service\OpenAIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EndingController extends AbstractController
{
    #[Route('/endings-feed/{emotion}', name: 'app_ending_index', methods: ['GET'])]
    public function index(EndingRepository $endingRepository, ?string $emotion = null): Response
    {
        dump($emotion);
        // Validate emotion parameter
        if ($emotion && in_array($emotion, ['sad', 'shocking', 'happy', 'angry', 'depressed', 'frustrated'])) {
            $endings = $endingRepository->findByEmotion($emotion);
        }
        else {
            $endings = $endingRepository->findAllOrderedByNewest();
        }
        return $this->render('ending/endingsfeed.html.twig', [
            'endings' => $endings,
            'emotion' => $emotion,
        ]);
    }

//    #[Route('/emotion/{emotion}', name: 'app_ending_by_emotion', methods: ['GET'])]
//    public function byEmotion(string $emotion, EndingRepository $endingRepository): Response
//    {
//        // Validate emotion parameter
//        if (!in_array($emotion, ['sad', 'shocking', 'happy', 'angry', 'depressed', 'frustrated'])) {
//            throw $this->createNotFoundException('Invalid emotion type');
//        }
//
//        return $this->render('user/ending/by_emotion.html.twig', [
//            'endings' => $endingRepository->findByEmotion($emotion),
//            'emotion' => $emotion,
//        ]);
//    }

    #[Route('/{id}', name: 'app_ending_show', methods: ['GET'])]
    public function show(Ending $ending, UserEndingInteractionRepository $interactionRepository, CommentRepository $commentRepository): Response
    {
        $userInteraction = null;
        if ($this->getUser()) {
            $userInteraction = $interactionRepository->findUserInteraction($this->getUser()->getId(), $ending->getId());
        }

        $comments = $commentRepository->findByEndingOrderedByNewest($ending->getId());

        return $this->render('user/ending/show.html.twig', [
            'ending' => $ending,
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
            'user_interaction' => $userInteraction,
            'comments' => $comments,
        ]);
    }
}
