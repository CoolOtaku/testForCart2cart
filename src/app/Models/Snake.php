<?php

namespace App\Models;

class Snake extends Animal
{
    public function getSoundFile(): string
    {
        return "https://www.google.com/logos/fnbx/animal_sounds/rattlesnake.mp3";
    }
}
