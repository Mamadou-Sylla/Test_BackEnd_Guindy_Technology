<?php

namespace App\Entity;

use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ApiResource(
    attributes: [
        "pagination_items_per_page" => 10
        ],
    normalizationContext: ['groups' => ['categories']],
    denormalizationContext: ['groups' => ['categories']],
    routePrefix:"/categories",
    collectionOperations: [
        'get_categories' => ["methods" => "GET", "path" => "", "route_name" => "get_categories"],
        'get_sous_categories' => ["methods" => "GET", "path" => "/sous-categories", "route_name" => "get_sous_categories"],
        'get_sous_sous_categories' => ["method" => "GET", "path" => "/sous-sous-categories", "route_name" => "get_sous_sous_categories"],
        'post' => ["path" => ""],
        'post_sous_categories' => ["method" => "POST", "path" => "/sous-categories", "route_name" => "post_sous_categories"],
        'post_sous_sous_categories' => ["method" => "POST", "path" => "/sous-sous-categories", "route_name" => "post_sous_sous_categories"],
    ],
    paginationEnabled: false,
    )]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["categories", "produits_read"])]
    private $id;

    #[ORM\Column(length: 255)]
    #[Groups(["categories", "produits_read"])]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $description = null;


    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Produit::class)]
    private Collection $produit;

    #[ORM\Column(nullable: true)]
    #[Groups(["categories", "produits_read"])]
    private ?int $type_categorie_id = null;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->produit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }

    public function getTypeCategorieId(): ?int
    {
        return $this->type_categorie_id;
    }

    public function setTypeCategorieId(?int $type_categorie_id): self
    {
        $this->type_categorie_id = $type_categorie_id;

        return $this;
    }

    
}
