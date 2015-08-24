<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\ItemConverter;
use Ddeboer\DataImport\ValueConverter;
use Ddeboer\DataImport\Filter;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\FixerValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;

class UserWorkflow extends Workflow
{
    public function addFilters()
    {
        $this->addFilter(new Filter\CallbackFilter(function ($data) {

            if (is_null($data['mail']) || empty($data['mail'])) {
                return false;
            }

            $mails = array(
                '09sp3.coupeG@laposte.net',
                'acoexister@gmail.com',
                'adn.lepacbo@orange.fr',
                'aja07@orange.fr',
                'alexandre.hagenauer@gmail.com',
                'bessma91@hotmail.fr',
                'bgauthier@nerim.net',
                'brian.boulle@hotmail.fr',
                'candice.manza@gmail.com',
                'centredesureaux@wanadoo.fr',
                'contact.hg@wanadoo.fr',
                'contact@guigonnet.com',
                'cs-directeur@orange.fr',
                'dersan@hotmail.fr',
                'dileo.chloe@gmail.com',
                'e.marris@wanadoo.fr',
                'ernst.elien@jem-assos.fr',
                'frederic.hugot@stcyraumontdor.fr',
                'henratpatrice@wanadoo.fr',
                'J.U.L.I.E.N-@hotmail.fr',
                'jean-francois.blanc@laligue-alpesdusud.org',
                'jean-paul.bruna@creps-strasbourg.sports.gouv.fr',
                'joporro@live.fr',
                'jujuorla@hotmail.fr',
                'kahina.saliha@hotmail.fr',
                'kawtar.faouz@laposte.net',
                'kouadiosait@gmail.com',
                'la-chanteuse-du-34@live.fr',
                'Lajulia34@hotmail.fr',
                'laure.sacksteder@hotmail.fr',
                'laurent@mountain-riders.org',
                'LEFRANCxMyriam@gmail.com',
                'lilla_irrony_theo@gmx.fr',
                'Lkbash@gmail.com',
                'love2jerome@hotmail.fr',
                'ludivine.mirabelli@live.fr',
                'lyse.mackitha@gmail.com',
                'm.pijassou@laposte.net',
                'mambi.jordan@gmail.com',
                'marielaure.paquier@sfr.fr',
                'melodiie@sfr.fr',
                'mjc-mazamet@wanadoo.fr',
                'nadine.faugeras@entraidescolaireamicale.org',
                'nolandu1395@gmail.com',
                'oumuoucoulibaly@yahoo.fr',
                'piatti.gwladys@hotmail.fr',
                'rocton_marie@yahoo.fr',
                'ronan.cabut@gmail.com',
                'rumelhart.frederic@gmail.com',
                'sellier_nicolas@live.fr',
                'september7th@live.fr',
                'torrya@hotmail.fr',
                'v.ducrocq@orange.fr',
                'victoria.rostan@voila.fr',
                'xAlLexandra@gmail.com',
                'yann190@hotmail.fr'
            );

            if (in_array($data['mail'], $mails)) {
                return false;
            }

            if ($data['mail'] == 'michael.lefloch@lanetscouade.com') {
                if (is_null($data['init']) || empty($data['init'])) {
                    return false;
                }
            }

            return isset($data['name']) && isset($data['profile']['field_structure']) && $data['profile']['field_structure'] && $data['uid'] != 1;
        }));
    }

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[uid]', '[originalId]')
            ->addMapping('[name]', '[username]')
            ->addMapping('[mail]', '[email]')
            ->addMapping('[pass]', '[password]')
            ->addMapping('[login]', '[lastLogin]')
            ->addMapping('[status]', '[enabled]')
            ->addMapping('[profile][changed]', '[updated]')
            ->addMapping('[profile][title]', '[firstname]')
            ->addMapping('[profile][field_identifiant][0][value]', '[lastname]')
            ->addMapping('[profile][field_structure][0][value]', '[type]')
            ;

        $this->addItemConverter($mappingItemConverter);

        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {

            // duplicate email issue
            if ($item['email'] == 'michael.lefloch@lanetscouade.com') {
                $item['email'] = $item['init'];
            }

            // default values
            $item['salt'] = '';
            $item['locked'] = 0;
            $item['expired'] = 0;
            $item['credentialsExpired'] = 0;

            $item['emailCanonical'] = mb_strtolower($item['email'], 'UTF-8');
            $item['usernameCanonical'] = mb_strtolower($item['username'], 'UTF-8');

            $roles = array('ROLE_USER');

            if (isset($item['type'])
                && in_array($item['type'], array(
                    'Une structure à la recherche de volontaires',
                    'Une structure a la recherche de volontaires'
                ))
            ) {
                $roles[] = 'ROLE_ORGANIZATION';
            } elseif (isset($item['type']) && in_array($item['type'], array(
                'En recherche dun volontariat',
                'En recherche d\'un volontariat',
                'Volontaire',
                'Ancien volontaire'
            ))) {
                $roles[] = 'ROLE_JEUNE';
            }

            $item['roles'] = serialize($roles);

            unset($item['profile']);

            return $item;
        }));
    }

    public function addValueConverters()
    {
        $typeFixer = new FixerValueConverter(array(
            'Volontaire'                                  => User::VOLUNTEER_TYPE,
            'En recherche dun volontariat'                => User::MISSION_SEEKER_TYPE,
            'En recherche d\'un volontariat'              => User::MISSION_SEEKER_TYPE,
            'Une structure à la recherche de volontaires' => User::ORGANIZATION_TYPE,
            'Une structure a la recherche de volontaires' => User::ORGANIZATION_TYPE,
            'Ancien volontaire'                           => User::FORMER_VOLUNTEER_TYPE,
            ''                                            => null
        ));

        $this
            ->addValueConverter('lastLogin', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('created', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('updated', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('type', $typeFixer)
        ;
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueUserBundle:User';
    }

}
