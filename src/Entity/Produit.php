<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(
    attributes: [
        "pagination_items_per_page" => 10
        ],
    normalizationContext: ['groups' => ['produits_read']],
    denormalizationContext: ['groups' => ['produits_write']],
    routePrefix:"/produits",
    collectionOperations: [
        'get' => ['path'=>''],
        'post' => ["method" => "POST", "path" => "", "route_name" => "post_produits"]
    ],
    itemOperations: [
        'get' => ['path'=>'/{id}'],
        'put' => ['path'=>'/{id}'],
        'delete' => ['path'=>'/{id}'],
    ],
    paginationEnabled: false,
    )]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["produits_read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["produits_read", "produits_write"])]
    #[Assert\NotBlank(message:"Le libelle est obligatoire")]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'produit')]
    private ?Categorie $categorie = null;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

   
}
