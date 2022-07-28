<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    
    #[Route('api/produits', methods: "POST", name: 'post_produits')]

    public function AddProduit(Request $request, ProduitRepository $repoProduit, CategorieRepository 
    $repoCategorie, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $result = $request->getContent();
        $data = $serializer->decode($result, "json");
        if(!isset($data['libelle'])) {
            # code...
            return new JsonResponse("Le libelle n'existe pas",Response::HTTP_BAD_REQUEST);
        }
        if (isset($data['categorie'])) {
            # code...
            $categorie = $data['categorie'];
        }
       
        else{
            return new JsonResponse("Le categorie n'existe pas",Response::HTTP_NOT_FOUND);
        }
       
        $id = explode('/', $categorie);
        $categorie = $repoCategorie->findOneBy(['id' => $id[3]]);
        if ($categorie->getTypeCategorieId() == null) {
            # code...
            $produit = $serializer->deserialize($result , Produit::class, 'json');
            $produit = $produit->setCategorie($categorie );
        }

        $manager->persist($produit);
        $manager->flush();

        return $this->json($produit,200);
    }
}
