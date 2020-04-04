<?php

namespace App\Command;

use App\Entity\ExchangeRate;
use App\Service\FetchFxRatesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

class FetchFxRatesCommand extends Command
{
    protected static $defaultName = 'app:fetch-fx-rates';

    private FetchFxRatesService $service;
    private EntityManagerInterface $em;

    public function __construct(FetchFxRatesService $service, EntityManagerInterface $em)
    {
        $this->service = $service;
        $this->em = $em;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://www.bank.lv/vk/ecb_rss.xml');
        $xmlString = $response->getContent();

        $rssFeedData = new \SimpleXMLElement($xmlString);

        foreach ($rssFeedData->channel->item as $item) {
            $ratesString = (string) $item->description;
            $date = new \DateTime($item->pubDate);

            preg_match_all("/\w{3}\s\d+\.\d+/", $ratesString, $currencyRates);

            foreach ($currencyRates[0] as $currencyRate) {
                $parts = explode(' ', $currencyRate);
                $currency = $parts[0];
                $rate = $parts[1];

                $rate = new ExchangeRate($currency, $rate, $date);
                $this->em->persist($rate);
            }
        }

        try {
            $this->em->flush();
        } catch (\Throwable $e) {

        }

//        $this->service->execute();
//
        $io = new SymfonyStyle($input, $output);
        $io->success('Fx rates fetched!');

        return 0;
    }
}
