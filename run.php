<?php

use App\TextUtil;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__.'/vendor/autoload.php'; // Composer's autoloader

$textUtil = new TextUtil();

$client = new Client();
$i = 0;
$cars = [];

$client->request('POST', 'https://naudotiauto.mollerauto.lt/lt/usedcars/search', [
    'ajaxsearch' => 1,
    'search_make' => '10002898',
    'search_model' => '10002918',
]);

$csvHandle = fopen('results.csv', 'wb+');
fputcsv($csvHandle, ['title', 'description', 'image', 'href', 'price', 'vat']);
$contents = \json_decode($client->getResponse()->getContent());
$domCrawler = new Crawler($contents->content);
$pages = $domCrawler->filter('.pagination li a')->count();

for ($page=1; $page<=$pages; $page++) {

    $client->request('POST', 'https://naudotiauto.mollerauto.lt/lt/usedcars/search', [
        'page' => $page,
    ]);
    $contents = \json_decode($client->getResponse()->getContent());
    $domCrawler = new Crawler($contents->content);
    /** @var DOMElement $item */
    foreach ($domCrawler->filter('.vehicle') as $item) {
        $crawler = new Crawler($item);


        $cars[$i]['title'] = $crawler->filter('.vehiclesummary')->text();
        $cars[$i]['description'] = $crawler->filter('.vehicledata')->first()->text();
        $cars[$i]['image'] = $textUtil->detectImage(
            $crawler->filter('.image a')->attr('style'),
            'https://naudotiauto.mollerauto.lt'
        );
        $cars[$i]['href'] = $textUtil->detectUrl(
            $crawler->filter('.image a')->attr('href'),
            'https://naudotiauto.mollerauto.lt'
        );

        $cars[$i]['price'] = $textUtil->detectPrice(
            $crawler->filter('.vehicledata')->eq(1)->text()
        );

        $cars[$i]['vat'] = $textUtil->detectVat(
            $crawler->filter('.vehicledata')->eq(1)->text()
        );

        fputcsv($csvHandle, $cars[$i]);
        $i++;
    }
}

fclose($csvHandle);
