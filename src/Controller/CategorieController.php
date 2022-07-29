<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('api/categories', methods: "GET", name: 'get_categories')]

    public function getCategories(SerializerInterface $serializer, CategorieRepository $repo): Response
    {
        $categories= $repo->findAll();
        $data = [];
            # code...
            $i = 0;
            foreach ($categories as $categorie) {
                if ($categorie->getTypeCategorieId() == null) {
                    # code...
                    $data[] = $categorie;
                }
                $i++;
            }
        return $this->json($data ,Response::HTTP_OK);
    }


    #[Route('api/categories/sous-categories', methods: "GET", name: 'get_sous_categories')]

    public function getSousCategories(SerializerInterface $serializer, CategorieRepository $repo): Response
    {
        $categories= $repo->findAll();

        $data = [];
            # code...
            $i = 0;
            foreach ($categories as $categorie) {
                if ($categorie->getTypeCategorieId() != null) {
                    # code...
                    $result = $repo->findOneBy(['id' => $categorie->getTypeCategorieId()]);
                    if($result->getTypeCategorieId() == null){
                        $data[] = $categorie;
                    }
                }
                $i++;
            }

        return $this->json($data ,Response::HTTP_OK);
    }

    #[Route('api/categories/sous-sous-categories', methods: "GET", name: 'get_sous_sous_categories')]

    public function getSousSousCategories(SerializerInterface $serializer, CategorieRepository $repo): Response
    {
        $categories= $repo->findAll();
        $data = [];
            # code...
            $i = 0;
            foreach ($categories as $categorie) {
                if ($categorie->getTypeCategorieId() != null) {
                    # code...
                    $result = $repo->findOneBy(['id' => $categorie->getTypeCategorieId()]);
                     if($result->getTypeCategorieId() != null){                   
                        $donnees = $result = $repo->findOneBy(['id' => $result->getTypeCategorieId()]);
                        if($donnees->getTypeCategorieId() == null){ 
                            $data[] = $categorie;                  
                        }
                    }
                }
                $i++;
            }
        return $this->json($data ,Response::HTTP_OK);
    }

    #[Route('api/categories/sous-categories', methods: "POST", name: 'post_sous_categories')]

    public function AddSousCategories(Request $request, CategorieRepository 
    $repoCategorie, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $result = $request->getContent();
        $data = $serializer->decode($result, "json");
         if (isset($data['type_categorie_id'])) {
            # code...
             $donnees = $data['type_categorie_id'];
         }
       
         else{
             return new JsonResponse("Le categorie ne peut pas etre nul",Response::HTTP_BAD_REQUEST);
         }
       
         $id = explode('/', $donnees);
        $categorie = $repoCategorie->findOneBy(['id' => $id[3]]);
         if ($categorie->getTypeCategorieId() == null) {
            # code...
            $sous_categorie = $serializer->deserialize($result , Categorie::class, 'json');
            $sous_categorie = $sous_categorie->setTypeCategorieId($categorie->getId());
        } 
        else{
            return new JsonResponse("ID n'est pas celui d'un categorie",Response::HTTP_BAD_REQUEST);
        }

        $manager->persist($sous_categorie);
        $manager->flush();

        return $this->json($sous_categorie,200);
    }

    #[Route('api/categories/sous-sous-categories', methods: "POST", name: 'post_sous_sous_categories')]

    public function AddSousSousCategories(Request $request, CategorieRepository 
    $repoCategorie, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $result = $request->getContent();
        $data = $serializer->decode($result, "json");
        // if(!isset($data['libelle'])) {
        //     # code...
        //     return new JsonResponse("Le libelle n'existe pas",Response::HTTP_BAD_REQUEST);
        // }
         if (isset($data['type_categorie_id'])) {
            # code...
             $donnees = $data['type_categorie_id'];
         }
       
         else{
             return new JsonResponse("Le sous categorie ne peut pas etre nul",Response::HTTP_BAD_REQUEST);
         }
       
         $id = explode('/', $donnees);
         $sous_categorie = $repoCategorie->findOneBy(['id' => $id[3]]);
         if ($sous_categorie) {
            # code...
            $categorie = $repoCategorie->findOneBy(['id' => $sous_categorie->getTypeCategorieId()]);
            if ($categorie->getTypeCategorieId() == null) {
                # code...
                $sous_sous_categorie = $serializer->deserialize($result , Categorie::class, 'json');
                $sous_sous_categorie = $sous_sous_categorie->setTypeCategorieId($sous_categorie->getId());
            } 
            else{
                return new JsonResponse("ID n'est pas celui d'un categorie",Response::HTTP_BAD_REQUEST);
            }
            $manager->persist($sous_sous_categorie);
            $manager->flush();
         }
         else{
            return new JsonResponse("L'identifiant sous categorie n'existe pas",Response::HTTP_NOT_FOUND);
         }

        return $this->json($sous_categorie,200);
    }
}
