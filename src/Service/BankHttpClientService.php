<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class BankHttpClientService
{
    const URL = 'https://www.bank.lv/vk/ecb_rss.xml';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getRssContent(): string
    {
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request(Request::METHOD_GET, static::URL);

            return $response->getContent();
        } catch (HttpExceptionInterface $e) {
            $this->logger->warning($e->getMessage(), $e->getTrace());

            return '';
        }
    }
}