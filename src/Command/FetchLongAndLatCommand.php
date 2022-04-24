<?php

namespace App\Command;

use App\Repository\GolfCourseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:fetch-long-and-lat',
    description: 'Fetches longitude and latitude for golf courses with missing data',
)]
class FetchLongAndLatCommand extends Command
{
    public function __construct(
        private GolfCourseRepository $golfCourseRepository,
        private HttpClientInterface $client,
        private ManagerRegistry $doctrine
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->doctrine->getManager();
        $io = new SymfonyStyle($input, $output);

        $golfCourses = $this->golfCourseRepository->findAll();
        foreach ($golfCourses as $golfCourse) {

            if(null !== $golfCourse->getLatitude() && null !== $golfCourse->getLongitude()) {
                continue;
            }

            $query = $golfCourse->getGeoname() ?? $golfCourse->getName();

            $response = $this->client->request(
                'GET',
                'https://nominatim.openstreetmap.org/search?format=json&polygon=1&addressdetails=1&q=' . $query
            );

            $resultJson = json_decode($response->getContent());

            if (isset($resultJson[0]->lat) && isset($resultJson[0]->lon) ) {
                $golfCourse->setLongitude($resultJson[0]->lon);
                $golfCourse->setLatitude($resultJson[0]->lat);
                $entityManager->persist($golfCourse);
                $entityManager->flush();
                $io->success('Geo data is saved');
            } else {
                $io->warning('Geo data could not be updated, check Geoname');
            }

        }


        return Command::SUCCESS;
    }
}
