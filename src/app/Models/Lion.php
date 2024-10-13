<?php

namespace App\Models;

class Lion extends Animal
{
    public function getSoundFile(): string
    {
        return "https://www.google.com/logos/fnbx/animal_sounds/lion.mp3";
    }
}
