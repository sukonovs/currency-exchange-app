<?php

namespace App\Command;

use App\Service\FetchFxRatesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchFxRatesCommand extends Command
{
    protected static $defaultName = 'app:fetch-fx-rates';

    private FetchFxRatesService $service;

    public function __construct(FetchFxRatesService $service)
    {
        $this->service = $service;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->execute();

        $io = new SymfonyStyle($input, $output);
        $io->success('Fx rates fetched!');

        return 0;
    }
}
