<?php

use App\Model\Car;
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
$emptyCar = new Car();
fputcsv($csvHandle, $emptyCar->getAttributes());
$contents = \json_decode($client->getResponse()->getContent());
$domCrawler = new Crawler($contents->content);
$pages = $domCrawler->filter('.pagination li a')->count();

$detailCrawler = new Client;

for ($page=1; $page<=$pages; $page++) {

    $client->request('POST', 'https://naudotiauto.mollerauto.lt/lt/usedcars/search', [
        'page' => $page,
    ]);
    $contents = \json_decode($client->getResponse()->getContent());
    $domCrawler = new Crawler($contents->content);
    /** @var DOMElement $item */
    foreach ($domCrawler->filter('.vehicle') as $item) {
        $crawler = new Crawler($item);
        $car = new Car();

        $description = $crawler->filter('.vehicledata')->first()->text();



        $car
            ->setTitle($crawler->filter('.vehiclesummary')->text())
            ->setDescription($description)
            ->setImage($textUtil->detectImage(
                $crawler->filter('.image a')->attr('style'),
                'https://naudotiauto.mollerauto.lt'
            ))
            ->setHref($textUtil->detectUrl(
                $crawler->filter('.image a')->attr('href'),
                'https://naudotiauto.mollerauto.lt'
            ))
            ->setPrice($textUtil->detectPrice(
                $crawler->filter('.vehicledata')->eq(1)->text()
            ))
            ->setVat($textUtil->detectVat(
                $crawler->filter('.vehicledata')->eq(1)->text()
            ))
            ->setProductionYear($textUtil->detectProductionYear($description))
            ->setPower($textUtil->detectPower($description))
            ->setGearbox($textUtil->detectGearbox($description))
            ->setBodyType($textUtil->detectBodyType($description))
            ->setFuel($textUtil->detectFuel($description));

        // $detailCrawler->request('GET', $car->getHref());
        // $a = $detailCrawler->getCrawler()->filter('.datatable:eq(1)')->text();
        // echo $a; die;

        fputcsv($csvHandle, $car->toArray());
        $i++;
    }
}

fclose($csvHandle);
