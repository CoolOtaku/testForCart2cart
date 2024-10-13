<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class IndexController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function index()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://zolotakraina.ua/ua/');
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $products = $crawler->filter('.product-items .product-item .product-item-info')
            ->each(function (Crawler $node) {
                $path_image = 'img.product-image-photo';
                $image = $node->filter($path_image)->count() > 0
                    ? $node->filter($path_image)->attr('src') : 'N/A';

                $path_name = '.product-item-details .product-item-name .product-item-link';
                $name = $node->filter($path_name)->count() > 0
                    ? $node->filter($path_name)->text() : 'N/A';

                $path_price = 'div.price-box';
                $price = $node->filter($path_price)->count() > 0
                    ? $node->filter($path_price)->outerHtml() : 'N/A';

                $price = $this->extractPrices(html_entity_decode($price));

                $path_url = '.product-item-photo';
                $url = $node->filter($path_url)->count() > 0
                    ? $node->filter($path_url)->attr('href') : 'N/A';

                return [
                    'image' => $image,
                    'name' => $name,
                    'price' => $price,
                    'url' => $url
                ];
            });

        return view('index', compact('products'));
    }

    private function extractPrices($html): string
    {
        preg_match('/"amount":\s*"(\d+)"/', $html, $specialPriceMatch);
        $specialPrice = isset($specialPriceMatch[1]) ? $specialPriceMatch[1] : 'N/A';
        return $specialPrice;
    }
}
