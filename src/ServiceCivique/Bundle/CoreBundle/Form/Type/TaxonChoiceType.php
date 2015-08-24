<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType as BaseTaxonChoiceType;

class TaxonChoiceType extends BaseTaxonChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $repository = $this->taxonRepository;
        $choiceList = function (Options $options) use ($repository) {
            $taxons = $repository->getTaxonsAsList($options['taxonomy']);

            if (null !== $options['filter']) {
                $taxons = array_filter($taxons, $options['filter']);
            }

            return new ObjectChoiceList($taxons, 'name', array(), null, 'id', null);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList
            ))
            ->setRequired(array(
                'taxonomy',
                'filter'
            ))
            ->setAllowedTypes(array(
                'taxonomy' => array('Sylius\Component\Taxonomy\Model\TaxonomyInterface'),
                'filter' => array('\Closure', 'null')
            ))
        ;
    }
}
