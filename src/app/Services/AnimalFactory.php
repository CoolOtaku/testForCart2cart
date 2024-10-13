<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Dog;
use App\Models\Cat;
use App\Models\Lion;
use App\Models\Mouse;
use App\Models\Snake;
use Exception;

class AnimalFactory {
    /**
     * @throws Exception
     */
    public static function createAnimal($type): Animal
    {
        return match ($type) {
            'dog' => new Dog(),
            'cat' => new Cat(),
            'mouse' => new Mouse(),
            'snake' => new Snake(),
            'lion' => new Lion(),
            default => throw new Exception("Unknown animal type"),
        };
    }
}
