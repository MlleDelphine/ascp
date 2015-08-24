<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Bazinga\Bundle\GeocoderBundle\Geocoder\LoggableGeocoder;
use Departements\Provider;
use Geocoder\Exception\NoResultException;

class RegionLocator
{

    protected $request;
    protected $geocoder;
    protected $departementProvider;

    public function __construct(Request $request, LoggableGeocoder $geocoder, Provider $provider)
    {
        $this->request = $request;
        $this->geocoder = $geocoder;
        $this->departementProvider = $provider;
    }

    /**
     * @return Departements\Model\Region $region or null
     */
    public function getRegion()
    {
        try {
            // Try to geocode user
            $geocoded = $this->geocoder
            ->using('custom_maxmind_binary')
            ->geocode($this->request->getClientIp());
        } catch (NoResultException $e) {
            return null;
        }

        // If user is geocoded in France
        if ($geocoded->getCountryCode() == 'FR') {
            return $this->departementProvider->findRegionByName($geocoded->getRegion());
        }

        return null;
    }

}
