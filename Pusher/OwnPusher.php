<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Sly\NotificationPusher\PushManager;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Adapter\Apns;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Model\Device;
use Openpp\PushNotificationBundle\Collections\DeviceCollection as Devices;

class OwnPusher extends AbstractPusher
{
    /**
     * {@inheritdoc}
     */
    public function push($application, $tagExpression, $message, array $options = array())
    {
        $application = $this->getApplication($application);

        $devices = $this->deviceManager->findDevicesByTagExpression($application, $tagExpression);

        if ($devices) {
            $this->pushToDevice($application, $devices, $message, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pushToDevice($application, $devices, $message, array $options = array())
    {
        $application = $this->getApplication($application);

        if (is_integer($devices[0])) {
            $devices = $this->deviceManager->findDevicesBy(array('id' => $devices));
        }
        $devices = new Devices(is_array($devices) ? $devices : $devices->toArray());

        $pushManager = new PushManager(PushManager::ENVIRONMENT_PROD);
        $messageObj  = new Message($message, $options);
        $timestamp   = new \DateTime();

        foreach (array_values(Device::getTypeChoices()) as $type) {
            $targetDevices = $devices->getByType($type);
            if (!$targetDevices->count()) {
                continue;
            }
            $deviceCollection = new DeviceCollection($targetDevices->toArray());

            $push = new Push($this->getAdapter($application, $type), $deviceCollection, $messageObj);
            $pushManager->add($push);
            $pushManager->push();
        }

        $this->dispatchPushResult($application, $message, $timestamp, $devices);
    }

    /**
     * Get the adapter.
     *
     * @param ApplicationInterface $application
     * @param integer $deviceType
     *
     * @return \Sly\NotificationPusher\Adapter\AdapterInterface
     */
    protected function getAdapter(ApplicationInterface $application, $deviceType)
    {
        switch ($deviceType) {
            case DeviceInterface::TYPE_ANDROID:
                $adapter = new Gcm(array(
                    'apiKey' => $application->getGcmApiKey()
                ));
                break;

            case DeviceInterface::TYPE_IOS:
                $adapter = new Apns(array(
                    'certificate' => $application->getApnsCertificate()
                ));
                break;
        }

        return $adapter;
    }
}