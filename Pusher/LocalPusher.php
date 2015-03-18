<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Symfony\Component\DependencyInjection\ContainerAware;

use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\MessageManagerInterface;
use Openpp\PushNotificationBundle\Model\PushManagerInterface;

use Sly\NotificationPusher\PushManager;
use Sly\NotificationPusher\Model\PushInterface;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\DeviceInterface;
use Sly\NotificationPusher\Adapter\Gcm as GcmAdapter;
use Sly\NotificationPusher\Adapter\Apns as ApnsAdapter;

class LocalPusher extends AbstactPusher
{
    const ADAPTER_GCM  = 'GCM';
    const ADAPTER_APNS = 'Apns';
    /**
     * @var DeviceManagerInterface
     */
    protected $deviceManager;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var PushManagerInterface
     */
    protected $pushManager;

    /**
     * @var PushManager
     */
    protected $pushServiceManager;

    /**
     * @var array
     */
    protected $adapters;

    /**
     * Constructor
     *
     * @param DeviceManagerInterface $deviceManager
     * @param MessageManagerInterface $messageManager
     * @param PushManagerInterface $pushManager
     */
    public function __construct(DeviceManagerInterface $deviceManager, MessageManagerInterface $messageManager, PushManagerInterface $pushManager)
    {
        $this->deviceManager = $deviceManager;
        $this->messageManager = $messageManager;
        $this->pushManager = $pushManager;

        $env = $this->container->getParameter('kernel.environment');
        $this->pushServiceManager = new PushManager($env === 'prod' ? PushManager::ENVIRONMENT_PROD : PushManager::ENVIRONMENT_DEV);

        $this->adapters = array();
    }

    /**
     * {@inheritdoc}
     */
    public function sendNotification($application, $devices, $message, array $options = array())
    {
        if (!isset($this->adapters[$application->getName()])) {
            $this->adapters[$application->getName()] = array();
        }
        if ($application->getGcmApiKey()) {
            if (!isset($this->adapters[$application->getName()][self::ADAPTER_GCM])) {
                $this->adapters[$application->getName()][self::ADAPTER_GCM] = new GcmAdapter(array('apiKey' => $application->getGcmApiKey()));
            }
        }
        if ($application->getApnsCertificate()) {
            if (!isset($this->adapters[$application->getName()][self::ADAPTER_APNS])) {
                $this->adapters[$application->getName()][self::ADAPTER_APNS] = new ApnsAdapter(array('certificate' => $application->getApnsCertificate()));
            }
        }

        if (false === $devices instanceof DeviceCollection
            || false === $devices instanceof DeviceInterface) {
            $devices = $this->getDevices($devices);
        }

        $message = $this->createMessage($message, $options);

        $push = $this->createPush($exampleAdapter, $devices, $message);
        $this->pushServiceManager->add($push);
        $this->pushServiceManager->push();
    }

    private function getDevices($devices)
    {
        
    }
}