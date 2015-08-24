<?php

namespace ServiceCivique\Bundle\CoreBundle\Geocoder\Provider;

use Geocoder\Provider\MaxMindBinaryProvider;

class CustomMaxMindBinaryProvider extends MaxMindBinaryProvider
{
    protected $regionCodesFile;

    /**
     * @param string   $datFile
     * @param int|null $openFlag
     * @param string   $regionCodesFile
     *
     * @throws RuntimeException         If maxmind's lib not installed.
     * @throws InvalidArgumentException If dat file is not correct (optional).
     */
    public function __construct($datFile, $openFlag = null, $regionCodesFile)
    {
        parent::__construct($datFile, $openFlag);
        $this->regionCodesFile = $regionCodesFile;
    }

    /**
     * {@inheritDoc}
     */
    public function getGeocodedData($address)
    {
        $result = parent::getGeocodedData($address);

        if (count($result) && isset($result[0]['region'])) {
            $result[0]['region'] = $this->resolveRegionCode($result[0]['region']);
        }

        return $result;
    }

    protected function resolveRegionCode($regionCode)
    {
        $output = $regionCode;

        if (($handle = fopen($this->regionCodesFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($regionCode == $data[1]) {
                    $output = $data[2];
                    break;
                }
            }
            fclose($handle);
        }

        return $output;
    }

}
