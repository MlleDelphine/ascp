<?php

namespace ServiceCivique\Bundle\UserBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface,
    FOS\UserBundle\Model\UserInterface,
    FOS\UserBundle\Mailer\MailerInterface,
    Hip\MandrillBundle\Dispatcher,
    Hip\MandrillBundle\Message,
    FOS\UserBundle\Mailer\TwigSwiftMailer;

class MandrillMailer extends TwigSwiftMailer
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $templating;

    /**
     * mailer
     *
     * @var object
     */
    protected $mailer;

    /**
     * Email templates to use and other parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * __construct
     *
     * @param object                $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface       $twig
     * @param array                 $parameters
     */
    public function __construct($mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, array $parameters)
    {
        $this->mailer     = $mailer;
        $this->router     = $router;
        $this->twig       = $twig;
        $this->parameters = $parameters;
    }

    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        try {
            $this->mailer->sendMessage($templateName, $context, $fromEmail, $toEmail);
        } catch (\Exception $e) {
        }
    }
}
