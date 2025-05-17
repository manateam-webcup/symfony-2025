<?php

namespace App\Controller;

use App\Entity\Ending;
use App\Form\EndingType;
use App\Repository\EndingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/ending')]
class EndingController extends AbstractController
{
    #[Route('/', name: 'app_ending_index', methods: ['GET'])]
    public function index(EndingRepository $endingRepository): Response
    {
        return $this->render('ending/index.html.twig', [
            'endings' => $endingRepository->findAllOrderedByNewest(),
        ]);
    }

    #[Route('/emotion/{emotion}', name: 'app_ending_by_emotion', methods: ['GET'])]
    public function byEmotion(string $emotion, EndingRepository $endingRepository): Response
    {
        // Validate emotion parameter
        if (!in_array($emotion, ['sad', 'shocking', 'happy', 'angry'])) {
            throw $this->createNotFoundException('Invalid emotion type');
        }

        return $this->render('ending/by_emotion.html.twig', [
            'endings' => $endingRepository->findByEmotion($emotion),
            'emotion' => $emotion,
        ]);
    }

    #[Route('/my-endings', name: 'app_ending_my_endings', methods: ['GET'])]
    public function myEndings(EndingRepository $endingRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('ending/my_endings.html.twig', [
            'endings' => $endingRepository->findByUser($user->getId()),
        ]);
    }

    #[Route('/new', name: 'app_ending_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ending = new Ending();
        $form = $this->createForm(EndingType::class, $ending);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the current user as the owner
            $ending->setUser($this->getUser());

            // Handle file uploads
            $this->handleFileUpload($form, $ending, 'image1', 'image1Path', $slugger);
            $this->handleFileUpload($form, $ending, 'image2', 'image2Path', $slugger);
            $this->handleFileUpload($form, $ending, 'image3', 'image3Path', $slugger);
            $this->handleFileUpload($form, $ending, 'audio', 'audioPath', $slugger);
            $this->handleFileUpload($form, $ending, 'video', 'videoPath', $slugger);

            $entityManager->persist($ending);
            $entityManager->flush();

            $this->addFlash('success', 'Your ending has been created!');

            return $this->redirectToRoute('app_ending_show', ['id' => $ending->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ending/new.html.twig', [
            'ending' => $ending,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ending_show', methods: ['GET'])]
    public function show(Ending $ending): Response
    {
        return $this->render('ending/show.html.twig', [
            'ending' => $ending,
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/admin/pending', name: 'admin_pending_endings', methods: ['GET'])]
    public function pendingEndings(EndingRepository $endingRepository): Response
    {
        // Only allow admins to access this page
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('ending/pending.html.twig', [
            'endings' => $endingRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/approve', name: 'app_ending_approve', methods: ['POST'])]
    public function approve(Ending $ending, EntityManagerInterface $entityManager): Response
    {
        // Only allow admins to approve endings
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ending->setStatus('approved');
        $entityManager->flush();

        $this->addFlash('success', 'Ending has been approved.');

        return $this->redirectToRoute('admin_pending_endings');
    }

    #[Route('/{id}/reject', name: 'app_ending_reject', methods: ['POST'])]
    public function reject(Ending $ending, EntityManagerInterface $entityManager): Response
    {
        // Only allow admins to reject endings
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ending->setStatus('rejected');
        $entityManager->flush();

        $this->addFlash('success', 'Ending has been rejected.');

        return $this->redirectToRoute('admin_pending_endings');
    }

    /**
     * Helper method to handle file uploads
     */
    private function handleFileUpload($form, Ending $ending, string $fieldName, string $propertyName, SluggerInterface $slugger): void
    {
        $file = $form->get($fieldName)->getData();

        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                // Update the appropriate property on the Ending entity
                $setter = 'set' . ucfirst($propertyName);
                $ending->$setter($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'There was a problem uploading your file: ' . $e->getMessage());
            }
        }
    }
}
