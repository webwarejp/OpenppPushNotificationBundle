<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\NotificationHubsRest\Notification\NotificationFactory;
use Openpp\NotificationHubsRest\NotificationHub\NotificationHub;
use Openpp\NotificationHubsRest\Registration\RegistrationFactory;
use Openpp\PushNotificationBundle\Exception\DeviceNotFoundException;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AzurePusher extends AbstractPusher
{
    const APNS_TEMPLATE_DEFAULT = '{"aps":{"alert":"$(message)"}}';
    const GCM_TEMPLATE_DEFAULT = '{"data":{"message":"$(message)"}}';

    /**
     * @var NotificationHub[]
     */
    protected $hubs;

    /**
     * @var NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var RegistrationFactory
     */
    protected $registrationFactory;

    /**
     * Initializes a new AzurePusher.
     *
     * @param ApplicationManagerInterface $applicationManager
     * @param TagManagerInterface         $tagManager
     * @param UserManagerInterface        $userManager
     * @param EventDispatcherInterface    $dispathcer
     * @param NotificationFactory         $notificationFactory
     * @param RegistrationFactory         $registrationFactory
     */
    public function __construct(
        ApplicationManagerInterface $applicationManager,
        TagManagerInterface         $tagManager,
        UserManagerInterface        $userManager,
        DeviceManagerInterface      $deviceManager,
        EventDispatcherInterface    $dispathcer,
        NotificationFactory         $notificationFactory,
        RegistrationFactory         $registrationFactory
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->registrationFactory = $registrationFactory;

        parent::__construct($applicationManager, $tagManager, $userManager, $deviceManager, $dispathcer);
    }

    /**
     * {@inheritdoc}
     */
    public function push($application, $tagExpression, $message, array $options = [])
    {
        $application = $this->getApplication($application);

        $notifications = $this->createNotifications($application, $tagExpression, $message, $options);

        foreach ($notifications as $notification) {
            $this->getHub($application)->sendNotification($notification);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pushToDevice($application, $devices, $message, array $options = [])
    {
        $application = $this->getApplication($application);

        if (is_int($devices[0])) {
            $devices = $this->deviceManager->findDevicesBy(['id' => $devices]);
        }

        foreach (array_chunk($devices, 20) as $chunk) {
            $tagExpression = '';
            foreach ($chunk as $device) {
                //TODO: specify unique device tag.
                $tagExpression = $tagExpression ? $tagExpression.' || '.$device->getUser()->getUidTag() : $device->getUser()->getUidTag();
            }
            $this->push($application, $tagExpression, $message, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $this->updateRegistration($applicationName, $deviceIdentifier, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $application = $this->getApplication($applicationName);

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);
        if (!$device) {
            throw new DeviceNotFoundException($applicationName."'s device ".$deviceIdentifier.'is not found.');
        }

        if (DeviceInterface::TYPE_IOS === $device->getType()) {
            $type = 'apple';
            $template = $application->getApnsTemplate() ? $application->getApnsTemplate() : self::APNS_TEMPLATE_DEFAULT;
        } else {
            $type = 'gcm';
            $template = $application->getGcmTemplate() ? $application->getGcmTemplate() : self::GCM_TEMPLATE_DEFAULT;
        }

        $registration = $this->registrationFactory->createRegistration($type);
        $registration->setToken($device->getToken())
                     ->setTemplate($template)
                     ->setRegistrationId($device->getRegistrationId())
                     ->setETag($device->getETag());

        if (!empty($tags)) {
            $registration->setTags($tags);
        }

        if (null === $device->getRegistrationId() || null === $device->getETag()) {
            $result = $this->getHub($application)->createRegistration($registration);
        } else {
            $result = $this->getHub($application)->updateRegistration($registration);
        }

        $device->setRegistrationId($result['RegistrationId']);
        $device->setETag($result['ETag']);
        $this->deviceManager->save($device);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag)
    {
        $application = $this->getApplication($applicationName);

        $deviceType = DeviceInterface::TYPE_IOS === $type ? 'apple' : 'gcm';

        $registration = $this->registrationFactory->createRegistration($deviceType);
        $registration->setRegistrationId($registrationId)
                     ->setETag($eTag);

        $this->getHub($application)->deleteRegistration($registration);
    }

    /**
     * Gets a Notification Hub for the application.
     *
     * @param ApplicationInterface $application
     *
     * @return NotificationHub
     */
    protected function getHub(ApplicationInterface $application)
    {
        if (!isset($this->hubs[$application->getHubName()])) {
            $this->hubs[$application->getHubName()] = new NotificationHub($application->getConnectionString(), $application->getHubName());
        }

        return $this->hubs[$application->getHubName()];
    }

    /**
     * Creates Notifications.
     *
     * @param ApplicationInterface $application
     * @param string               $tagExpression
     * @param string               $message
     * @param array                $options
     *
     * @return \Openpp\NotificationHubsRest\Notification\NotificationInterface[]
     */
    protected function createNotifications(ApplicationInterface $application, $tagExpression, $message, array $options)
    {
        $notifications = [];

        if (is_string($message)) {
            $message = ['message' => $message];
        } elseif (!is_array($message)) {
            throw new \InvalidArgumentException('Invalid message type.');
        }

        $notifications[] = $this->notificationFactory->createNotification('template', $message, $options, $tagExpression);

        return $notifications;
    }
}
