<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NextButton extends Component
{

    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function render(): View|Closure|string
    {
        return view('components.next-button');
    }
}
