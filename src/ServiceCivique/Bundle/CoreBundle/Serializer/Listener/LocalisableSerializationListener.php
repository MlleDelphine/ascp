<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class LocalisableSerializationListener implements EventSubscriberInterface
{
    public function __construct($provider, $geocoder)
    {
        $this->provider           = $provider;
        $this->geocoder           = $geocoder;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Mission',
                'method' => 'onPostSerialize'
            ),
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Profile',
                'method' => 'onPostSerialize'
            ),
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Organization',
                'method' => 'onPostSerialize'
            )
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        $this->addLocationData($object, $visitor);
    }

    protected function addLocationData($object, $visitor)
    {
        $location = null;
        $providers = $this->geocoder->getProviders();

        if (method_exists($object, 'getDepartment') && $object->getDepartment()) {
            $departement = $this->provider->findDepartementByCode($object->getDepartment());
            $visitor->addData('department_name', $departement->getName());

            // to avoid memory leaks especially in es populate command we use departement_geocoder this way
            $result = $providers['departement_geocoder']->getGeocodedData($departement->getName());

            if(!empty($result)) {
                $location = array(
                    (double) $result['longitude'],
                    (double) $result['latitude'],
                );
            }


        } elseif (method_exists($object, 'getCountry') && $object->getCountry()) {

            // to avoid memory leaks especially in es populate command we use country_geocoder this way
            $result = $providers['country_geocoder']->getGeocodedData($object->getCountry());

            if(!empty($result)) {
                $location = array(
                    (double) $result['longitude'],
                    (double) $result['latitude'],
                );
            }
        }

        if ($location) {
            $visitor->addData('location', $location);
        }
    }
}
