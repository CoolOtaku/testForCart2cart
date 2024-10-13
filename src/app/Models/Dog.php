<?php

namespace App\Models;

class Dog extends Animal
{
    public function getSoundFile(): string
    {
        return "https://www.google.com/logos/fnbx/animal_sounds/dog.mp3";
    }
}
