<?php

namespace Openpp\PushNotificationBundle\Listener;

use Openpp\PushNotificationBundle\Event\PostPushEvent;

class PushResultEmailListener
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param array             $config
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $config)
    {
        $this->mailer = $mailer;
        $this->twig   = $twig;
        $this->config = $config;
    }

    /**
     * Handle event.
     *
     * @param PostPushEvent $event
     */
    public function onPushed(PostPushEvent $event)
    {
        if (isset($this->config['email'])) {
            $context = array(
                'application' => $event->getApplication(),
                'message'     => $event->getMessage(),
                'timestamp'   => $event->getTimestamp(),
                'counts'      => $event->getCounts(),
            );
            $this->sendEmail(
                $this->config['email']['template'],
                $context,
                $this->config['email']['from'],
                $this->config['email']['to']
            );
        }
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $from
     * @param string $to
     */
    protected function sendEmail($templateName, $context, $from, $to)
    {
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', array('subject' => $this->config['email']['subject']));
        $textBody = $template->renderBlock('body_text', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($textBody);

        $this->mailer->send($message);
    }
}