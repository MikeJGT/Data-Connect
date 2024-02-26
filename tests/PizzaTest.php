<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Pizza;
use App\Factory\PizzaFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PizzaTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testGetCollection(): void
    {
        PizzaFactory::createMany(100);

        static::createClient()->request(
            'GET',
            '/api/pizzas?page=1'
        );
        $this->assertResponseIsSuccessful(message: 'The response failed');

        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8',
            message: 'The content-type is not the same'
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/Pizza',
            '@id' => '/api/pizzas',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/pizzas?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/pizzas?page=1',
                'hydra:last' => '/api/pizzas?page=20',
                'hydra:next' => '/api/pizzas?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/api/pizzas{?name}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'name',
                        'property' => 'name',
                        'required' => false
                    ]
                ]
            ]
        ]);
    }

    public function testCreatePizza()
    {
        static::createClient()->request(
            'POST',
            '/api/pizzas',
            ['json' => [
                'name' => 'Carbonara',
                'ingredients' => ['Cream', 'Bacon', 'Queso', 'Champs'],
                'ovenTimeInSeconds' => 10000,
                'special' => true
            ]]
        );
        $this->assertResponseIsSuccessful(message: 'The response failed');
        $this->assertResponseStatusCodeSame(201, message: 'The status code is not 201.');

        $this->assertResponseHasHeader(
            'content-type',
            'application/json; charset=utf-8',
            'The content-type is not the same'
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/Pizza',
            'name' => 'Carbonara',
            'ingredients' => [
                'Cream',
                'Bacon',
                'Queso',
                'Champs'
            ],
            'ovenTimeInSeconds' => 10000,
            'special' => true
        ]);
    }

    public function testCreatedInvalidPizza()
    {
        static::createClient()->request(
            'POST',
            '/api/pizzas',
            ['json' => [
                'name' => '+48Chars->12345678901234567890123456789012345678901234567890',
                'ingredients' => [
                    'Cream',
                    'Bacon',
                    'Queso',
                    'Champs',
                    'Onion',
                    'Cream',
                    'Bacon',
                    'Queso',
                    'Champs',
                    'Onion',
                    'Cream',
                    'Bacon',
                    'Queso',
                    'Champs',
                    'Onion',
                    'Cream',
                    'Bacon',
                    'Queso',
                    'Champs',
                    'Onion',
                    'Cream',
                    'Bacon',
                    '+20 Ingredients'
                ],
                'special' => true
            ]]
        );

        $this->assertResponseIsUnprocessable(message: 'The response failed');
        $this->assertResponseStatusCodeSame(422, message: 'The status code should be 422');

        $this->assertJsonContains([
            "status" => 422,
            "violations" => [
                [
                    "propertyPath" => "name",
                    "message" => "This value is too long. It should have 48 characters or less."
                ],
                [
                    "propertyPath" => "ingredients",
                    "message" => "You cannot specify more than 20 ingredients"
                ]
            ],
            "detail" => "name: This value is too long. It should have 48 characters or less.\ningredients: You cannot specify more than 20 ingredients",
            "title" => "An error occurred"
        ]);
    }

    public function testUpdatePizza()
    {
        PizzaFactory::createMany(100);

        $iri = $this->findIriBy(Pizza::class, ['id' => '9']);

        static::createClient()->request('PATCH', $iri, [
            'json' => [
                'name' => 'Carbonara',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);


        $this->assertResponseIsSuccessful(message: 'The response failed');

        $this->assertResponseStatusCodeSame(200, message: 'The status code is not 200.');

        $this->assertResponseHasHeader(
            'content-type',
            'application/ld+json; charset=utf-8',
            'The content-type is not the same'
        );
        $this->assertMatchesJsonSchema([
            'updatedAt' => 'string',
        ]);
    }

    public function testUpdateInvalidPizza()
    {
        PizzaFactory::createOne(
            [
                'name' => 'invalid pizza',
                'special' => true
            ]
        );
        $iri = $this->findIriBy(Pizza::class, ['name' => 'invalid pizza']);

        static::createClient()->request('PATCH', $iri, [
            'json' => [
                'special' => false,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful(message: 'The response failed');

        $this->assertResponseStatusCodeSame(200, message: 'The status code is not 200.');

        $this->assertResponseHasHeader(
            'content-type',
            'application/ld+json; charset=utf-8',
            'The content-type is not the same'
        );


        $special = static::getContainer()->get('doctrine')->getRepository(Pizza::class)->findOneBy(['name' => 'invalid pizza']);
        $this->assertTrue($special->getSpecial());
    }

    public function testDeletePizza()
    {
        PizzaFactory::createMany(100);

        static::createClient()->request(
            'DELETE',
            '/api/pizzas/1'
        );

        $this->assertResponseIsSuccessful(message: 'The response failed');

        $this->assertResponseStatusCodeSame(204, message: 'The status code is not 204.');

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Pizza::class)->findOneBy(['id' => '1'])
        );
    }

    public function testPutMethodIsNotAllowed()
    {
        PizzaFactory::createOne(
            [
                'name' => 'Cannot put pizza',
                'special' => true
            ]
        );

        $iri = $this->findIriBy(Pizza::class, ['name' => 'Cannot put pizza']);

        static::createClient()->request('PUT', $iri, [
            'json' => [
                'special' => false,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseStatusCodeSame(405, message: 'The status code is not 405. Modify this test if you want to use the PUT method.');
    }
}
