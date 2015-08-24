<?php

namespace ServiceCivique\Bundle\CoreBundle\Geocoder\Provider;

use Geocoder\Provider\ProviderInterface;
use PhpCollection\Map;

class DepartementGeocoder implements ProviderInterface
{
    private $geocodeFilePath;
    private $datas;

    public function __construct($geocodeFilePath)
    {
        $this->geocodeFilePath = $geocodeFilePath;
        $this->datas = new Map();
    }

    private function loadDatas()
    {
        if (!$this->datas->isEmpty()) {
            return;
        }

        $this->datas->setAll(json_decode(file_get_contents($this->geocodeFilePath), true));
    }

    public function getGeocodedData($address)
    {
        $this->loadDatas();

        return array();

        $data = $this->datas->get($address)->getOrElse(null);

        if (!$data) {
            return array();
        }

        return array(
            'latitude'  => $data['lat'],
            'longitude' => $data['lng']
        );
    }

    public function getReversedData(array $coordinates)
    {
        return null;
    }

    public function getName()
    {
        return 'departement_geocoder';
    }

    public function setMaxResults($maxResults)
    {
        return $this;
    }
}
