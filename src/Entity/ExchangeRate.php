<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRateRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="currency_date_idx", columns={"currency", "date"})})
 */
class ExchangeRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $currency;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $rate;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $date;

    public function __construct(string $currency, string $rate, DateTimeInterface $date)
    {
        $this->currency = $currency;
        $this->rate = $rate;
        $this->date = $date;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
