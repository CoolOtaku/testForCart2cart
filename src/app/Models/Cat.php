<?php

namespace App\Models;

class Cat extends Animal
{
    public function getSoundFile(): string
    {
        return "https://www.google.com/logos/fnbx/animal_sounds/cat.mp3";
    }
}
