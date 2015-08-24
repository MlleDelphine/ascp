<?php

namespace ServiceCivique\Bundle\MailerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;

class UpdateMandrillTemplatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:update_mandrill_templates')
            ->setDescription('Update mandrill templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container =  $this->getContainer();

        $rootDir         = $container->get('kernel')->getRootDir();
        $templateManager = $container->get('service_civique_mailer.mandrill_template_manager');
        $templating      = $container->get('templating');

        $finder = new Finder();

        // find twig templates files
        $templates = $finder->files()
            ->in($rootDir . '/../src/ServiceCivique/Bundle/MailerBundle/Resources/views/Mandrill')
            ->name('*.html.twig')
            ->notName('layout.html.twig');

        $templateNames = $this->getExistingTemplateNames($templateManager);

        // render twig templates and send them to mandrill
        foreach ($templates as $template) {

            $content = $templating->render('ServiceCiviqueMailerBundle:Mandrill:' . $template->getBasename());

            $templateName = $template->getBasename('.html.twig');
            $method = in_array($templateName, $templateNames) ? 'update' : 'add';

            $templateManager->{$method}(
                $templateName,
                null,
                null,
                null,
                $content,
                null,
                true,
                array()
            );
        }

    }

    protected function getExistingTemplateNames($templateManager)
    {
        $existingTemplates = $templateManager->getList();
        $existingTemplatesNames = array();

        foreach ($existingTemplates as $existingTemplate) {
            $existingTemplatesNames[] = $existingTemplate['name'];
        }

        return $existingTemplatesNames;
    }
}
