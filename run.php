<?php

use App\Collector\CollectorInterface;
use App\Collector\MollerAuto;
use App\Model\Car;
use Goutte\Client;

require __DIR__.'/vendor/autoload.php'; // Composer's autoloader

/** @var CollectorInterface[] $collectors */
$collectors = [
    new MollerAuto(new Client(), '10002898', '10002918'),
    new MollerAuto(new Client(), '10002898', '10002928'),
];

$cars = [];

$csvHandle = fopen('results.csv', 'wb+');
$emptyCar = new Car();
fputcsv($csvHandle, $emptyCar->getAttributes());
foreach ($collectors as $collector) {
    $cars = array_merge($cars, $collector->get());
}
foreach ($cars as $car) {
    fputcsv($csvHandle, $car->toArray());
}

fclose($csvHandle);
