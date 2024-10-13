<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AnimalFactory;
use App\Models\Dog;
use App\Models\Cat;
use App\Models\Lion;
use App\Models\Mouse;
use App\Models\Snake;
use Exception;

class AnimalFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_dog_instance()
    {
        $animal = AnimalFactory::createAnimal('dog');
        $this->assertInstanceOf(Dog::class, $animal);
    }

    /** @test */
    public function it_creates_a_cat_instance()
    {
        $animal = AnimalFactory::createAnimal('cat');
        $this->assertInstanceOf(Cat::class, $animal);
    }

    /** @test */
    public function it_creates_a_mouse_instance()
    {
        $animal = AnimalFactory::createAnimal('mouse');
        $this->assertInstanceOf(Mouse::class, $animal);
    }

    /** @test */
    public function it_creates_a_snake_instance()
    {
        $animal = AnimalFactory::createAnimal('snake');
        $this->assertInstanceOf(Snake::class, $animal);
    }

    /** @test */
    public function it_creates_a_lion_instance()
    {
        $animal = AnimalFactory::createAnimal('lion');
        $this->assertInstanceOf(Lion::class, $animal);
    }

    /** @test */
    public function it_throws_exception_for_unknown_animal_type()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown animal type");

        AnimalFactory::createAnimal('unknown');
    }
}
