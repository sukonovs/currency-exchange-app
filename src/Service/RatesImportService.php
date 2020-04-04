<?php


namespace App\Service;


use App\Components\RatesImport;
use App\Entity\ExchangeRate;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;

class RatesImportService
{
    private BankHttpClientService $http;
    private LoggerInterface $logger;

    public function __construct(BankHttpClientService $http, LoggerInterface $logger)
    {
        $this->http = $http;
        $this->logger = $logger;
    }

    public function importRates(): RatesImport
    {
        $importContent = $this->http->getRssContent();

        try {
            return $this->parseContent($importContent);
        } catch (\Throwable $e) {
            $this->logger->critical(
                'Failed to parse RSS content',
                [
                    'content' => $importContent,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
            );

            return new RatesImport(new ArrayCollection());
        }
    }

    public function parseContent(string $importContent): RatesImport
    {
        $collection = new ArrayCollection();

        if ($importContent === '') {
            return new RatesImport($collection);
        }

        $rssFeedData = new \SimpleXMLElement($importContent);

        foreach ($rssFeedData->channel->item as $item) {
            $ratesString = (string)$item->description;
            $date = new \DateTime($item->pubDate);

            preg_match_all("/\w{3}\s\d+\.\d+/", $ratesString, $currencyRates);

            foreach ($currencyRates[0] as $currencyRate) {
                $parts = explode(' ', $currencyRate);
                $currency = $parts[0];
                $rate = $parts[1];

                $collection->add(new ExchangeRate($currency, $rate, $date));
            }
        }

        return new RatesImport($collection);
    }
}