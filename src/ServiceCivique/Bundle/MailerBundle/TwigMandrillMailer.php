<?php

namespace ServiceCivique\Bundle\MailerBundle;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Hip\MandrillBundle\Dispatcher;
use Hip\MandrillBundle\Message;

class TwigMandrillMailer
{
    protected $dispatcher;
    protected $router;
    protected $twig;

    /**
     * __construct
     *
     * @param Dispatcher            $dispatcher
     * @param UrlGeneratorInterface $router
     * @param \Twig_Environment     $twig
     */
    public function __construct(Dispatcher $dispatcher, UrlGeneratorInterface $router, \Twig_Environment $twig)
    {
        $this->dispatcher = $dispatcher;
        $this->router     = $router;
        $this->twig       = $twig;
    }

    /**
     * sendMessage
     * @TODO rename this method in createMessageAndSend
     *
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    public function sendMessage($templateName, $context = array(), $fromEmail = null, $toEmail)
    {
        $message = $this->createNewMessage($templateName, $context, $fromEmail, $toEmail);

        return $this->send($message);
    }

    /**
     * send
     *
     * @param Message $message
     * @param string  $templateName
     * @param array   $templateContent
     * @param mixed   $async
     * @throw Mandrill_Error
     */
    public function send(Message $message, $templateName = 'default', $templateContent = array(), $async = false)
    {
        try {
            return $this->dispatcher->send($message, $templateName, $templateContent, $async);
        } catch (\Mandrill_Error $e) {
            return false;
        }
    }

    /**
     * createNewMessage
     *
     * @param  mixed   $templateName
     * @param  array   $context
     * @param  mixed   $fromEmail
     * @param  mixed   $toEmail
     * @return Message
     */
    public function createNewMessage($templateName, $context = array(), $fromEmail = null, $toEmail)
    {
        $default_context  = $this->getDefaultContext();

        $context  = array_merge_recursive($default_context, $context);
        $context  = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject  = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = new Message();

        if (is_array($toEmail)) {
            $sendTo = array_shift($toEmail);
            $message->addTo($sendTo);
            // CC
            $multiple = false;
            if (is_array($toEmail)) {
                $multiple = true;
                foreach ($toEmail as $ccEmail) {
                    $message->addTo($ccEmail, $ccEmail, 'cc');
                }
            } else {
                $message->addTo($toEmail, $toEmail, 'cc');
            }
        } else {
            $message->addTo($toEmail);
        }
        if ($fromEmail != null) {
            $message->setFromEmail($fromEmail);
        }
        $message->setSubject($subject);
        $message->setTrackClicks(true);

        $message->addGlobalMergeVar('TEXT', $htmlBody);
        $message->addGlobalMergeVar('TITLE', $subject);

        $message->addGlobalMergeVar('MULTICLASS', ($multiple ? 'multiple' : 'solo'));


        $message->addGlobalMergeVar(
            'SUB_TITLE',
            $this->getFormattedDate(new \DateTime())
        );

        return $message;
    }

    /**
     * getDefaultContext
     * @return array
     */
    protected function getDefaultContext()
    {
        return array(
            'website' => $this->router->getContext()->getScheme() . '://' . $this->router->getContext()->getHost()
        );
    }

    /**
     * getFormattedDate
     *
     * @param \DateTime $date
     */
    protected function getFormattedDate(\DateTime $date)
    {
        return $date->format('d') . ' ' . $this->getFrenchMonth($date->format('n')) . ' ' . $date->format('Y');
    }

    /**
     * getFrenchMonth
     * @TODO clean this
     *
     * @param integer $key
     */
    protected function getFrenchMonth($key)
    {
        $months = array(
            1  => 'Janvier',
            2  => 'Février',
            3  => 'Mars',
            4  => 'Avril',
            5  => 'Mai',
            6  => 'Juin',
            7  => 'Juillet',
            8  => 'Août',
            9  => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        );

        return $months[$key];
    }
}
