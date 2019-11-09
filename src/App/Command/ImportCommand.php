<?php

namespace App\Command;

use App\Collector\CollectorInterface;
use App\Collector\DasWeltAuto\DasWeltAuto;
use App\Collector\MollerAuto\MollerAuto;
use App\Collector\Polizinginiai\Polizinginiai;
use App\Model\Car;
use App\Model\CarCollection;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('app:import')
            ->setDescription('Import car information and output to csv');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting import!');

        /** @var CollectorInterface[] $collectors */
        $collectors = [
            // new MollerAuto(new Client(), '10002898', '10002918'),
            new MollerAuto(new Client(), '10002898', '10002928'),
            // new Polizinginiai(new Client(),'volkswagen', 'passat'),
            new DasWeltAuto(new Client(), '10002898', '10002928')
        ];

        $cars = new CarCollection();

        $csvHandle = fopen('results.csv', 'wb+');
        $emptyCar = new Car();
        fputcsv($csvHandle, $emptyCar->getAttributes());
        foreach ($collectors as $collector) {
            $output->writeln('Fetching car collection from ' . get_class($collector));
            foreach ($collector->get() as $car) {
                $cars->add($car);
            }
            // $cars = array_merge($cars, $collector->get());
        }

        foreach ($cars as $car) {
            fputcsv($csvHandle, $car->toArray());
        }

        $output->writeln('Output written to results.csv');

        fclose($csvHandle);
    }
}
