<?php

namespace App\Story;

use App\Factory\PizzaFactory;
use Zenstruck\Foundry\Story;

final class PizzaStory extends Story
{
    public function build(): void
    {
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
        PizzaFactory::createMany(100);
    }
    
}
