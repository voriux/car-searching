<?php
/**
 * @copyright C UAB NFQ Technologies 2019
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 *
 */

namespace App\Collector\Polizinginiai;

use App\Collector\CollectorInterface;
use App\Model\Car;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Polizinginiai implements CollectorInterface
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
    private $url = 'https://www.polizinginiai.lt/lt/naudoti-automobiliai';

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

        $this->client->request('GET', $this->getUrl());
        $domCrawler = new Crawler($this->client->getResponse()->getContent());

        foreach ($domCrawler->filter('.newest__usedcars .car__info') as $item) {
            $car = new Car();
            $crawler = new Crawler($item);
            $car
                ->setTitle(
                    $this->textUtil->detectTitle($crawler->filter('.car__detail h4')->text())
                )
                ->setDescription('')
                ->setImage('')
                ->setHref($this->textUtil->detectHref($crawler->filter('a')->attr('href')))
                ->setPrice($this->textUtil->detectPrice(
                    $crawler->filter('.car__price .price__inner')->text()
                ))
                ->setVat(21)
                ->setFinalPrice($this->textUtil->detectPrice(
                    $crawler->filter('.car__price .price__inner')->text()
                ))
                ->setProductionYear(trim($crawler->filter('.car__detail .car__date')->eq(0)->text()))
                ->setPower($this->textUtil->detectPower(
                    $crawler->filter('.car__detail .power__info')->text()
                ))
                ->setGearbox(trim($crawler->filter('.car__about .rightinfo .gear__info span')->text()))
                ->setBodyType($this->textUtil->detectBodyType(
                    $crawler->filter('.car__about .rightinfo .type__info span')->text()
                ))
                ->setFuel($this->textUtil->detectFuel(
                    $crawler->filter('.car__about .leftinfo .gas__info span')->text())
                )
                ->setKm($this->textUtil->detectKm(
                    $crawler->filter('.car__about .rightinfo .speed__info span')->text()
                ));

            $cars[] = $car;
        }

        return $cars;
    }

    protected function getUrl()
    {
        return $this->url . '/' . $this->make . '-' . $this->model;
    }
}