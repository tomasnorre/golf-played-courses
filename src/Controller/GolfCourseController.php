<?php

namespace App\Controller;

use App\Entity\GolfCourse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GolfCourseController extends AbstractController
{
    #[Route('/golf/course', name: 'app_golf_course')]
    public function index(ManagerRegistry $doctrine ): Response
    {

        $repository = $doctrine->getRepository(GolfCourse::class);


        return $this->render('golf_course/index.html.twig', [
            'golfCourses' => $repository->findWithMarker(),
            'controller_name' => 'GolfCourseController',
        ]);
    }
}
