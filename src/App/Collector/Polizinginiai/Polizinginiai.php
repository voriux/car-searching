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

        // $contents = \json_decode($this->client->getResponse()->getContent());
        $domCrawler = new Crawler($this->client->getResponse()->getContent());

        $detailCrawler = clone $this->client;

        foreach ($domCrawler->filter('.search-rezultatai .automobilis') as $item) {
            $car = new Car();
            $crawler = new Crawler($item);
            $car
                ->setTitle(
                    $this->textUtil->detectTitle($crawler->filter('.automobilis-info h2')->text())
                )
                ->setDescription('')
                ->setImage('')
                ->setHref($this->textUtil->detectHref($crawler->filter('a')->attr('href')))
                ->setPrice($this->textUtil->detectPrice(
                    $crawler->filter('.bazine-kaina span')->text()
                ))
                ->setVat(21)
                ->setFinalPrice($this->textUtil->detectPrice(
                    $crawler->filter('.bazine-kaina span')->text()
                ))
                ->setProductionYear(trim($crawler->filter('.info-juostele span')->eq(0)->text()))
                ->setPower(0)
                ->setGearbox(trim($crawler->filter('.info-juostele span')->eq(4)->text()))
                ->setBodyType('N/A')
                ->setFuel($this->textUtil->detectFuel($crawler->filter('.info-juostele span')->eq(2)->text()))
                ->setKm((int)trim($crawler->filter('.info-juostele span')->eq(3)->text()));

            $detailCrawler->request('GET', $car->getHref());
            $detailInfo = $detailCrawler
                ->getCrawler()
                ->filter('div.auto-info > div.table > div:nth-child(5) > span.value > span')
                ->text();

            $car->setPower($this->textUtil->detectPower($detailInfo));

            $cars[] = $car;
        }

        return $cars;
    }

    protected function getUrl()
    {
        return $this->url . '/' . $this->make . '-' . $this->model;
    }
}