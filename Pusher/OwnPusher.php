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


class OwnPusher extends AbstractPusher
{
    /**
     * {@inheritdoc}
     */
    public function push($applicationName, $target, $message, array $options = array())
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);
        if (!$application) {
            throw new ApplicationNotFoundException($applicationName . ' is not found.');
        }

        $pushManager = new PushManager(PushManager::ENVIRONMENT_PROD);
        $message     = new Message($message, $options);

        $devices = $this->deviceManager->findDevicesByTagExpression($application, $target);

        if ($devices) {
            foreach (array(DeviceInterface::TYPE_ANDROID, DeviceInterface::TYPE_IOS) as $type) {
                $targetDevices = $devices->filter(function ($d) use ($type) {
                    return $d->getType() == $type;
                });

                if (!$targetDevices->count()) {
                    continue;
                }
                $deviceCollection = new DeviceCollection($targetDevices->toArray());

                $push = new Push($this->getAdapter($application, $type), $deviceCollection, $message);
                $pushManager->add($push);
                $pushCollection = $pushManager->push();
            }
        }
    }

    /**
     * Get the adapter.
     *
     * @param ApplicationInterface $application
     * @param unknown $deviceType
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