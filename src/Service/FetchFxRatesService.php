<?php

namespace App\Service;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;

class FetchFxRatesService
{
    private ExchangeRateRepository $repo;
    private RatesImportService $importService;

    public function __construct(ExchangeRateRepository $repo, RatesImportService $importService)
    {
        $this->repo = $repo;
        $this->importService = $importService;
    }

    public function execute(): void
    {
        $import = $this->importService->importRates();

        $exchangeRate = $this->repo->findLast();
        $rates = $import->getRates();

        if ($exchangeRate) {
            $rates = $rates->filter(fn(ExchangeRate $rate) => $rate->isNewer($exchangeRate));
        }

        $this->repo->saveRates($rates);
    }
}