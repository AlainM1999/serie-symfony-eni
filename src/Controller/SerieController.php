<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/series', name: 'serie_')]
class SerieController extends AbstractController
{
    //todo: aller chercher les series en bdd
    #[Route('', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findBestSeries();

        return $this->render('serie/list.html.twig', [
            'series' => $series,

        ]);
    }

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('cheh');
        }

        return $this->render('serie/details.html.twig', [
            'serie' => $serie

        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Serie $serie, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime());

        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            $entityManager->persist($serie);
            $entityManager->flush();

            $this->addFlash('success','Serie added! Good job.');
            return $this->redirectToRoute('serie_details',['id' =>$serie->getId()]);

        }

        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm->createView() //pas obligatoire le ->createView()


        ]);
    }




//    #[Route('/demo', name: 'em-demo')]
//    public function demo(EntityManagerInterface $entityManager): Response
//    {
//        $serie = new Serie();
//        $serie->setName('pif');
//        $serie->setBackdrop('sdfq');
//        $serie->setPoster('sfdg');
//        $serie->setDateCreated(new \DateTime());
//        $serie->setFirstAirDate(new \DateTime('- 1 year'));
//        $serie->setLastAirDate(new \DateTime('- 6 month'));
//        $serie->setGenres('drama');
//        $serie->setOverview('blablabla');
//        $serie->setPopularity(123.00);
//        $serie->setVote(8.2);
//        $serie->setStatus('canceled');
//        $serie->setTmdbId(329432);
//
//        dump($serie);
//
//        $entityManager->persist($serie);
//        $entityManager->flush();
//
//        dump($serie);
//
//        $serie->setGenres('comedy');
//
//        //$entityManager->remove($serie);
//        $entityManager->flush();
//
//
//        //$entityManager = $this->getDoctrine()->getManager();
//
//        return $this->render('serie/create.html.twig');
//    }
}
