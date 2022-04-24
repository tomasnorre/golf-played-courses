<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\GolfCourse;
use App\Repository\CountryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-golf-courses',
    description: 'Import golf courses from CSV',
)]
class ImportGolfCoursesCommand extends Command
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private CountryRepository $countryRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('csv', InputArgument::REQUIRED, 'Path to CSV file to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csv = $input->getArgument('csv');
        $entityManager = $this->doctrine->getManager();

        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $golfCourse = new GolfCourse();
                $golfCourse->setName($data[0]);
                $golfCourse->setComment($data[1]);
                if(!$country = $this->getCountry($data[2])) {
                    $io->error(sprintf('Country %s not found', $data[2]));
                    return Command::FAILURE;
                }
                $golfCourse->setCountry($country);
                $golfCourse->setLongitude((float) $data[3]);
                $golfCourse->setLatitude((float) $data[4]);
                $entityManager->persist($golfCourse);
                $entityManager->flush();
                $io->success('Golf Course data is saved for ' . $data[0]);
            }
            fclose($handle);
        }

        return Command::SUCCESS;
    }

    private function getCountry(string $country): ?Country
    {
        $country = explode(" ", $country);
        return $this->countryRepository->findOneBy(['name' => $country[0]]);
    }
}
