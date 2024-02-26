<?php

namespace App\Factory;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Pizza>
 *
 * @method        Pizza|Proxy                     create(array|callable $attributes = [])
 * @method static Pizza|Proxy                     createOne(array $attributes = [])
 * @method static Pizza|Proxy                     find(object|array|mixed $criteria)
 * @method static Pizza|Proxy                     findOrCreate(array $attributes)
 * @method static Pizza|Proxy                     first(string $sortedField = 'id')
 * @method static Pizza|Proxy                     last(string $sortedField = 'id')
 * @method static Pizza|Proxy                     random(array $attributes = [])
 * @method static Pizza|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PizzaRepository|RepositoryProxy repository()
 * @method static Pizza[]|Proxy[]                 all()
 * @method static Pizza[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Pizza[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Pizza[]|Proxy[]                 findBy(array $attributes)
 * @method static Pizza[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Pizza[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PizzaFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'ingredients' => ["Ingredient1", "Ingredient2"],
            'name' => self::faker()->text(),
            'ovenTimeInSeconds' => self::faker()->randomNumber(4),
            'special' => self::faker()->boolean(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Pizza $pizza): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Pizza::class;
    }
}
