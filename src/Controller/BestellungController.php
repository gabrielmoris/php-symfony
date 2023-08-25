<?php

namespace App\Controller;

use App\Entity\Bestellung;
use App\Entity\Gericht;
use App\Repository\BestellungRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BestellungController extends AbstractController
{
    #[Route('/bestellung', name: 'bestellung')]
    public function index(BestellungRepository $bestellungRepository): Response
    {
        $bestellungen = $bestellungRepository->findBy(
            ['tisch'=>'tisch1']
        );
        return $this->render('bestellung/index.html.twig', [
            'bestellungen' => $bestellungen,
        ]);
    }

    #[Route('/bestellen/{id}', name: 'bestellen')]
    public function bestellen(Gericht $gericht,ManagerRegistry $doctrine){

        $bestellung = new Bestellung();
        $bestellung->setTisch('tisch1');
        $bestellung->setName($gericht->getName());
        $bestellung->setBnummer($gericht->getId());
        $bestellung->setPreis($gericht->getPreis());
        $bestellung->setStatus('offen');

        // entity/manager
        $em = $doctrine->getManager();
        $em->persist($bestellung);
        $em->flush();

        $this->addFlash('bestell',$bestellung->getName(). ' wurde zur Bestellung hinzugefügt');

        return $this->redirect($this->generateUrl('menu'));
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status, ManagerRegistry $doctrine){
          // entity/manager
          $em = $doctrine->getManager();
          $bestellung=$em->getRepository(Bestellung::class)->find($id);

          $bestellung->setStatus($status);
          $em->flush();

          return $this->redirect($this->generateUrl('bestellung'));
    }

    
    #[Route('/loeschen/{id}', name: 'loeschen')]
    public function loeschen(ManagerRegistry $doctrine, $id, BestellungRepository $br): Response
    {
        $em = $doctrine->getManager();
        $bestellung = $br->find($id);
        $em->remove($bestellung);
        $em->flush();

        return $this->redirect($this->generateUrl('bestellung'));
    }
}
