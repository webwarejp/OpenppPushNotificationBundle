<?php

namespace Openpp\PushNotificationBundle\Consumer;

use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\HistoryManagerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TrackConsumer implements ConsumerInterface
{
    const TYPE_NAME = 'openpp.push_notification.track';

    const OPERATION_DELIVERED = 'delivered';
    const OPERATION_CLICK = 'click';

    /**
     * @var DeviceManagerInterface
     */
    protected $deviceManager;

    /**
     * @var HistoryManagerInterface
     */
    protected $historyManager;

    /**
     * Initializes a new TrackConsumer.
     *
     * @param DeviceManagerInterface  $deviceManager
     * @param HistoryManagerInterface $historyManager
     */
    public function __construct(DeviceManagerInterface $deviceManager, HistoryManagerInterface $historyManager)
    {
        $this->deviceManager = $deviceManager;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $operation = $event->getMessage()->getValue('operation');
        $notificationId = $event->getMessage()->getValue('notificationId');
        $subscriptionId = $event->getMessage()->getValue('subscriptionId');

        $history = $this->historyManager->findHistoryBy(['notificationId' => $notificationId]);
        if (empty($history)) {
            throw new NotFoundHttpException(sprintf('No history(%s) found.', $notificationId));
        }

        $device = $this->deviceManager->findDeviceBy(['token' => $subscriptionId]);

        switch ($operation) {
            case self::OPERATION_DELIVERED:
                if (!empty($device)) {
                    $device->setLastDeliveredNotificationId($notificationId);
                    $this->deviceManager->save($device);
                }
                $history->setDeliveredCount($history->getDeliveredCount() + 1);
                $this->historyManager->save($history);

                break;

            case self::OPERATION_CLICK:
                $history->setClickCount($history->getClickCount() + 1);
                $this->historyManager->save($history);

                break;
        }
    }
}
