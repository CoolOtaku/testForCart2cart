<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\IndexController;

class IndexControllerTest extends TestCase
{
    /** @test */
    public function it_correctly_extracts_price_from_html()
    {
        $htmlWithPrice = '"amount": "1200"';
        $htmlWithoutPrice = 'No price here';

        $controller = new IndexController();

        $price = $controller->extractPrices($htmlWithPrice);

        $this->assertEquals('1200', $price);

        $noPrice = $controller->extractPrices($htmlWithoutPrice);

        $this->assertEquals('N/A', $noPrice);
    }
}
