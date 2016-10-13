<?php

namespace Openpp\PushNotificationBundle\Listener;

use Openpp\PushNotificationBundle\Event\PostPushEvent;
use Openpp\PushNotificationBundle\model\HistoryManagerInterface;

class PushHistoryListener
{
    /**
     * @var HistoryManagerInterface
     */
    protected $historyManager;

    /**
     * Constructor
     *
     * @param HistoryManagerInterface     $historyManager
     */
    public function __construct(HistoryManagerInterface $historyManager)
    {
        $this->historyManager = $historyManager;
    }

    /**
     * Handle event.
     *
     * @param PostPushEvent $event
     */
    public function onPushed(PostPushEvent $event)
    {
        $history = $this->historyManager->create();
        $history->setApplication($event->getApplication())
                ->setNotificationId($event->getNotificationId())
                ->setMessage($event->getMessage())
                ->setPushedAt($event->getTimestamp())
                ->setSentCount(array_sum($event->getCounts()))
        ;
        $options = $event->getOptions();
        if (isset($options['title'])) {
            $history->setTitle($options['title']);
        }
        if (isset($options['icon'])) {
            $history->setIconUrl($options['icon']);
        }
        if (isset($options['url'])) {
            $history->setUrl($options['url']);
        }

        $this->historyManager->save($history);
    }
}