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
        $counter = 0;
        foreach ($golfCourses as $golfCourse) {

            /*if($golfCourse->getLatitude() !== null || $golfCourse->getLongitude() !== null) {
                continue;
            }*/

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
                //$io->success('Geo data is saved for ' . $golfCourse->getName());

            } else {
                $golfCourse->setLatitude(0.0);
                $golfCourse->setLongitude(0.0);
                $entityManager->persist($golfCourse);
                $entityManager->flush();
                $io->warning('Geo data could not be updated, check Geoname for ' . $golfCourse->getName());
                $counter++;
            }

        }

        $io->warning('Could not update for: ' . $counter);

        return Command::SUCCESS;
    }
}
