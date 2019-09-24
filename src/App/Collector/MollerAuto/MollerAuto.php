<?php

namespace App\Collector\MollerAuto;

use App\Collector\CollectorInterface;
use App\Model\Car;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class MollerAuto implements CollectorInterface
{
    /**
     * @var string
     */
    private $make;

    /**
     * @var string
     */
    private $model;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var TextUtil
     */
    private $textUtil;

    /**
     * @var string
     */
    private $url = 'https://naudotiauto.mollerauto.lt/lt/usedcars/search';

    /**
     * @param Client $client
     * @param string $make
     * @param string $model
     */
    public function __construct(Client $client, string $make, string $model)
    {
        $this->make = $make;
        $this->model = $model;
        $this->client = $client;
        $this->textUtil = new TextUtil();
    }

    /**
     * @return Car[]|array
     */
    public function get(): array
    {
        $cars = [];

        $this->client->request('POST', $this->url, [
            'ajaxsearch' => 1,
            'search_make' => $this->make,
            'search_model' => $this->model,
        ]);

        $contents = \json_decode($this->client->getResponse()->getContent());
        $domCrawler = new Crawler($contents->content);
        $pages = $domCrawler->filter('.pagination li a')->count();

        $detailCrawler = clone $this->client;

        for ($page=1; $page<=$pages; $page++) {

            $this->client->request('POST', $this->url, [
                'page' => $page,
            ]);
            $contents = \json_decode($this->client->getResponse()->getContent());
            $domCrawler = new Crawler($contents->content);
            /** @var \DOMElement $item */
            foreach ($domCrawler->filter('.vehicle') as $item) {
                $crawler = new Crawler($item);
                $car = new Car();

                $description = $crawler->filter('.vehicledata')->first()->text();

                $car
                    ->setTitle($crawler->filter('.vehiclesummary')->text())
                    ->setDescription($description)
                    ->setImage($this->textUtil->detectImage(
                        $crawler->filter('.image a')->attr('style'),
                        'https://naudotiauto.mollerauto.lt'
                    ))
                    ->setHref($this->textUtil->detectUrl(
                        $crawler->filter('.image a')->attr('href'),
                        'https://naudotiauto.mollerauto.lt'
                    ))
                    ->setPrice($this->textUtil->detectPrice(
                        $crawler->filter('.vehicledata')->eq(1)->text()
                    ))
                    ->setVat($this->textUtil->detectVat(
                        $crawler->filter('.vehicledata')->eq(1)->text()
                    ))
                    ->setProductionYear($this->textUtil->detectProductionYear($description))
                    ->setPower($this->textUtil->detectPower($description))
                    ->setGearbox($this->textUtil->detectGearbox($description))
                    ->setBodyType($this->textUtil->detectBodyType($description))
                    ->setFuel($this->textUtil->detectFuel($description));

                $detailCrawler->request('GET', $car->getHref());
                $a = $detailCrawler
                    ->getCrawler()
                    ->filter('.datatable')
                    ->eq(1)
                    ->filter('tr:nth-child(2) > td');

                $car->setKm($this->textUtil->detectKm($a->text()));
                $car->setFinalPrice(round((1+(21-$car->getVat())/100)*$car->getPrice()));
                $car->setExternalId($this->textUtil->detectExternalId($car->getHref()));

                $cars[] = $car;
            }
        }
        return $cars;
    }
}
