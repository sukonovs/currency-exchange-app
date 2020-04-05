<?php
namespace App\Twig;

use Alcohol\ISO4217;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('to_countries', [$this, 'toCountries']),
        ];
    }

    public function toCountries($alpha3)
    {
        $iso4217 = new ISO4217();
        $currency = $iso4217->getByAlpha3($alpha3);

        $countryCodes = (array) $currency['country'];

        $flags = [];
        foreach ($countryCodes as $countryCode) {
            $country = country($countryCode);
            $flags[] = sprintf("<code>%s</code>%s", $countryCode, $country->getEmoji());
        }

        return implode(' ', $flags);
    }
}