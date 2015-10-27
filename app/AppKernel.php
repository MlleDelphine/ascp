<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Polem\DepartementsBundle\PolemDepartementsBundle(),
            new Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),

            new ServiceCivique\Bundle\CoreBundle\ServiceCiviqueCoreBundle(),
            new ServiceCivique\Bundle\WebBundle\ServiceCiviqueWebBundle(),
            new ServiceCivique\Bundle\ImporterBundle\ServiceCiviqueImporterBundle(),
            new ServiceCivique\Bundle\ArchiveBundle\ServiceCiviqueArchiveBundle(),

            new Ornicar\ApcBundle\OrnicarApcBundle(),

            new Sonata\SeoBundle\SonataSeoBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new ServiceCivique\Bundle\UserBundle\ServiceCiviqueUserBundle(),

            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),
            new Hip\MandrillBundle\HipMandrillBundle(),
            new ServiceCivique\Bundle\MailerBundle\ServiceCiviqueMailerBundle(),
            new ServiceCivique\Bundle\ContactBundle\ServiceCiviqueContactBundle(),
            new ServiceCivique\Bundle\ExporterBundle\ServiceCiviqueExporterBundle(),
            new ServiceCivique\Bundle\KeyValueStoreBundle\ServiceCiviqueKeyValueStoreBundle(),
            new ServiceCivique\Bundle\AddressingBundle\ServiceCiviqueAddressingBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),

            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            // new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new ServiceCivique\Bundle\ContentBundle\ServiceCiviqueContentBundle(),
            new ServiceCivique\Bundle\SeoBundle\ServiceCiviqueSeoBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),

            new Liip\MonitorBundle\LiipMonitorBundle(),
            new Igorw\FileServeBundle\IgorwFileServeBundle(),
            new Lns\Bundle\MenuBundle\LnsMenuBundle(),
            new ServiceCivique\Bundle\MenuBundle\ServiceCiviqueMenuBundle(),
            new Eko\FeedBundle\EkoFeedBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
