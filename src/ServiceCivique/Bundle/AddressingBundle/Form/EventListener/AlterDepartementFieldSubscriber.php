<?php

namespace ServiceCivique\Bundle\AddressingBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Departements\Provider;

class AlterDepartementFieldSubscriber implements EventSubscriberInterface
{
    protected $departmentProvider;
    protected $config;

    /**
     * __construct
     *
     * @param Provider $departementProvider
     * @param array    $config
     */
    public function __construct(Provider $departementProvider, $config = array())
    {
        $defaultConfig = array();

        $this->departementProvider = $departementProvider;
        $this->config = array_merge($defaultConfig, $config);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT  => 'postSubmitDataAndPreSetData',
            FormEvents::PRE_SET_DATA => 'postSubmitDataAndPreSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmitData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmitDataAndPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $config = $this->config;

        if ($data != null && $data->getDepartment()) {
            $departement = $this->departementProvider->findDepartementByCode($data->getDepartment());

            // if region of selected departement is different from selected region
            // then take selected region and reset the departemnt data
            if ($departement && $data->getArea() != null && $departement->getRegion()->getCode() != $data->getArea()) {
                $data->setDepartment('');
            }

            // if department isset and area is not set
            // then set the area with departement area code
            if ($departement && $data->getArea() == null) {
                $data->setArea($departement->getRegion()->getCode());
            }
        }

        if (($event->getName() == FormEvents::PRE_SET_DATA)
        && ($data != null && $data->getArea() != null && $data->getArea() != "")) {
            $config['region'] = $data->getArea();
            $form->add('location', 'location', $config);
        }

        $event->setData($data);
    }

    public function preSubmitData(FormEvent $event)
    {
        $data   = $event->getData();
        $form   = $event->getForm();
        $config = $this->config;

        if ($form->has('location') && isset($data['location'])) {
            $form->remove('location');
            if (isset($data['location']['area']) && $data['location']['area'] != null && $data['location']['area'] != "") {
                $config['region'] = $data['location']['area'];
            }
            $form->add('location', 'location', $config);
        }
    }

}
