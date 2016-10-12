<?php

namespace Openpp\PushNotificationBundle\Manipurator;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\MapBundle\Model\PointManagerInterface;

class RegistrationManipurator
{
    /**
     * @var ApplicationManagerInterface
     */
    protected $applicationManager;

    /**
     * @var DeviceManagerInterface
     */
    protected $deviceManager;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var TagManagerInterface
     */
    protected $tagManager;

    /**
     * @var PointManagerInterface
     */
    protected $pointManager;

    /**
     * Constructor
     *
     * @param ApplicationManagerInterface $applicationManager
     * @param DeviceManagerInterface $deviceManager
     * @param UserManagerInterface $userManager
     * @param TagManagerInterface $tagManager
     */
    public function __construct(
        ApplicationManagerInterface $applicationManager,
        DeviceManagerInterface $deviceManager,
        UserManagerInterface $userManager,
        TagManagerInterface $tagManager
    ) {
        $this->applicationManager = $applicationManager;
        $this->deviceManager      = $deviceManager;
        $this->userManager        = $userManager;
        $this->tagManager         = $tagManager;
    }

    /**
     * @param PointManagerInterface $pointManager
     */
    public function setPointManager(PointManagerInterface $pointManager)
    {
        $this->pointManager = $pointManager;
    }

    /**
     * Registers a device to the application.
     *
     * @param string  $applicationId
     * @param string  $deviceIdentifier
     * @param string  $token
     * @param string  $uid
     * @param integer $type
     * @param float   $locationLatitude
     * @param float   $locationLongitude
     *
     * @throws ApplicationNotFoundException
     *
     * @return array
     */
    public function registerDevice($applicationId, $deviceIdentifier, $token, $uid, $locationLatitude, $locationLongitude, $type, $key = null, $auth = null)
    {
        $application = $this->applicationManager->findApplicationBy(array('slug' => $applicationId));

        if (is_null($application)) {
            throw new ApplicationNotFoundException('Application ' . $applicationId . ' is not found.');
        }

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (is_null($device)) {
            // This implementation is assumed that the device identifier is device's AdvertisingID.
            // So, the device identifier may be changed.
            // Let us search the device by the token.
            $device = $this->deviceManager->findDeviceByToken($application, $token);

            if (is_null($device)) {
                $device = $this->deviceManager->create();
            }
        }

        $device->setApplication($application);
        $device->setDeviceIdentifier($deviceIdentifier);
        $device->setToken($token);
        $device->setType($type);
        $device->setPublicKey($key);
        $device->setAuthtoken($auth);
        if (!$device->getRegisteredAt()) {
            $device->setRegisteredAt(new \DateTime());
        }
        $device->setUnregisteredAt(null);

        if (null !== $locationLatitude && null !== $locationLongitude) {
            if ($location = $device->getLocation()) {
                $point = $location->getPoint();
                if ($point->getLatitude() != $locationLatitude || $point->getLongitude() != $locationLongitude) {
                    $newPoint = clone $point;
                    $newPoint
                        ->setLatitude($locationLatitude)
                        ->setLongitude($locationLongitude)
                    ;
                    $location->setPoint($newPoint);
                }
            } else {
                if (!empty($this->pointManager)) {
                    $device->setLocation($this->pointManager->createFromLonLat($locationLongitude, $locationLatitude));
                }
            }
        }

        $user = $device->getUser();
        if (is_null($user) || $user->getUid() != $uid) {
            if ($uid) {
                $user = $this->userManager->findUserByUid($application, $uid);
            }
            if (is_null($user)) {
                $user = $this->userManager->create();
                if (is_null($uid)) {
                    $uid = uniqid('pseudo_');
                }
            }
        }

        $user->setApplication($application);
        $user->setUid($uid);

        $device->setUser($user);
        $user->addDevice($device);

        $application->addUser($user);

        $this->deviceManager->save($device);

        return array(
            'deviceIdentifier' => $deviceIdentifier,
            'uid' => $uid,
            'registrationDate' => $device->getUpdatedAt(),
        );
    }

    /**
     * Unregisters a device from the application.
     *
     * @param string $applicationId
     * @param string $deviceIdentifier
     *
     * @throws ApplicationNotFoundException
     *
     * @return array
     */
    public function unregisterDevice($applicationId, $deviceIdentifier)
    {
        $application = $this->applicationManager->findApplicationBy(array('slug' => $applicationId));

        if (is_null($application)) {
            throw new ApplicationNotFoundException('Application ' . $applicationId . ' is not found.');
        }

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (!is_null($device)) {
            $device->setUnregisteredAt(new \DateTime());
            $device->getUser()->setBadge(0);
            $this->deviceManager->save($device);

            return array(
                'deviceIdentifier' => $deviceIdentifier,
                'uid' => $device->getUser()->getUid(),
                'unregistrationDate' => $device->getUnregisteredAt(),
            );
        }

        return array('message' => 'No device.');
    }
}