<?php

namespace App\Collector\DasWeltAuto;

use App\Collector\CollectorInterface;
use App\Model\Car;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class DasWeltAuto implements CollectorInterface
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
    private $url = 'https://www.dasweltauto.lt/lt/search';

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
     * @return array|Car[]
     */
    public function get(): array
    {
        $cars = [];
        $contents = $this->getContents();

        $domCrawler = new Crawler($contents->content);
        $detailCrawler = clone $this->client;

        $totalPages = $domCrawler->filter('nav .page_links ul li')->count();
        for ($i=1; $i<$totalPages; $i++) {
            $this->client->request('POST', 'https://www.dasweltauto.lt/lt/search/showresults?', ['set_page' => $i], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
            $pageContents = \json_decode($this->client->getResponse()->getContent());
            $pageCrawler = new Crawler($pageContents->content);
            foreach ($pageCrawler->filter('article.car_info') as $item) {
                $crawler = new Crawler($item);
                $car = new Car();
                $car
                    ->setTitle($crawler->filter('.block_title a')->text())
                    ->setHref($this->textUtil->detectHref($crawler->filter('.block_title a')->attr('href')))
                    ->setPrice($this->textUtil->detectPrice($crawler->filter('.block_price h2')->text()))
                    ->setVat($this->textUtil->detectVat($crawler->filter('.block_price h2 div')->text()))
                    ->setProductionYear($this->textUtil->detectProductionYear($crawler->filter('.dwa_ptable div')->first()->text()))
                    ->setPower($this->textUtil->detectPower($crawler->filter('.dwa_ptable div')->eq(2)->text()))
                    ->setKm($this->textUtil->detectKm($crawler->filter('.dwa_ptable div')->eq(3)->text()))
                    ->setFuel($this->textUtil->detectFuel($crawler->filter('.dwa_ptable div')->eq(1)->text()));

                $car->setFinalPrice(round((1 + (21 - $car->getVat()) / 100) * $car->getPrice()));
                $car->setExternalId($this->textUtil->detectExternalId($car->getHref()));

                // $detailCrawler->request('GET', $car->getHref());
                //
                // $gearBox = $detailCrawler
                //     ->getCrawler()
                //     ->filter('#dwa_tab_1 > dl > dd:nth-child(32)')->text();
                //
                // $bodyType = $detailCrawler
                //     ->getCrawler()
                //     ->filter('#dwa_tab_1 > dl > dd:nth-child(28)')->text();
                //
                // $car
                //     ->setGearbox($this->textUtil->detectGearbox($gearBox))
                //     ->setBodyType($this->textUtil->detectBodyType($bodyType));

                $cars[] = $car;
            }
        }
        return $cars;
    }

    /**
     * @return mixed
     */
    protected function getContents()
    {
        $this->client->request('GET', $this->url);

        $this->client->request('POST', 'https://www.dasweltauto.lt/lt/search/updatefilter', [
            'changed_param' => 'make',
            'search_make' => $this->make,
            'search_mileage' => '',
            'search_price' => '',
            'search_firstreg' => '',
            'search_power' => '',
        ], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);



        $this->client->request('POST', 'https://www.dasweltauto.lt/lt/search/updatefilter', [
            'changed_param' => 'model',
            'search_model' => $this->model,
            'search_mileage' => '',
            'search_price' => '',
            'search_firstreg' => '',
            'search_power' => '',
        ], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $this->client->request('POST', 'https://www.dasweltauto.lt/lt/search/showresults?', [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        return \json_decode($this->client->getResponse()->getContent());
    }
}