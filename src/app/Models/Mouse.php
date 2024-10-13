<?php

namespace App\Models;

class Mouse extends Animal
{
    public function getSoundFile(): string
    {
        return "https://www.google.com/logos/fnbx/animal_sounds/rat.mp3";
    }
}
