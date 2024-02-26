<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
// use ApiPlatform\Metadata\Put;

use App\Repository\PizzaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PizzaRepository::class)]
#[
    ApiResource(
        description: 'An Api with the best Pizzas.',
        // The global pagination is configured for 20
        // Change the pagination for this ApiResource in the next line.
        paginationItemsPerPage: 5,
        normalizationContext: ['groups' => ['read']],
        denormalizationContext: ['groups' => ['write']],
        operations: [
            new Get(),
            new GetCollection(
                //Uncoment the next line for change the GetCollection endpoint.
                // uriTemplate: "/pizzas/v2/collection"
            ),
            new Post(),

            // Uncomment the next line to allow the PUT method
            // and change the tests. 
            // new Put(),
            new Patch(),
            new Delete(),
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name' => SearchFilter::STRATEGY_PARTIAL
        ]
    )
]
class Pizza
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;



    #[
        Assert\NotBlank,
        Assert\Length(max: 48),
        Groups(['read', 'write'])
    ]
    #[ORM\Column(type: Types::STRING)]
    private ?string $name = null;


    #[
        Assert\NotNull,
        Assert\Count(
            min: 1,
            max: 20,
            minMessage: 'You cannot specify less than 1 ingredients',
            maxMessage: 'You cannot specify more than {{ limit }} ingredients',
        ),
        Groups(['read', 'write'])
    ]
    #[ORM\Column(type: Types::JSON)]
    private array $ingredients = [];

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $ovenTimeInSeconds = null;

    /**
     * This field is created automatically when a new Pizza is created on POST (or replaced compleatly on PUT).
     */
    #[
        Assert\NotNull,
        Groups(['read'])
    ]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    /**
     * This field is updated automatically on POST and PATCH.
     */
    #[Groups(['read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * This field just can be setted when a new Pizza is created, POST method.
     */
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $special;


    public function __construct(bool $special)
    {
        $this->special =  $special;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->setUpdatedAt();
        $this->name = $name;

        return $this;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function setIngredients(array $ingredients): static
    {

        $this->setUpdatedAt();
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getOvenTimeInSeconds(): ?int
    {
        return $this->ovenTimeInSeconds;
    }

    public function setOvenTimeInSeconds(?int $ovenTimeInSeconds): static
    {
        $this->setUpdatedAt();
        $this->ovenTimeInSeconds = $ovenTimeInSeconds;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function setUpdatedAt(): static
    {
        $formatTime = 'Y-m-d H:i:s';
        $updatedTime = new DateTimeImmutable();

        if ($this->createdAt->format($formatTime) != $updatedTime->format($formatTime)) {
            $this->updatedAt = $updatedTime;
        }

        return $this;
    }

    public function getSpecial(): bool
    {
        return $this->special;
    }
}
