<?php

namespace App\Components;

use Doctrine\Common\Collections\ArrayCollection;

class RatesImport
{
    private ArrayCollection $rates;

    public function __construct(ArrayCollection $rates)
    {
          $this->rates = $rates;
    }

    public function getRates(): ArrayCollection
    {
        return $this->rates;
    }
}