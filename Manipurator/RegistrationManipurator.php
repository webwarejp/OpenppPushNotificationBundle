<?php

namespace Openpp\PushNotificationBundle\Manipurator;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\MapBundle\Model\PointManagerInterface;
use Openpp\PushNotificationBundle\Exception\DeviceNotFoundException;
use Openpp\PushNotificationBundle\Model\Device;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;

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
     * @var string
     */
    protected $uidAutoPrefix;

    /**
     * @var PointManagerInterface
     */
    protected $pointManager;

    /**
     * Initializes a new RegistrationManipurator.
     *
     * @param ApplicationManagerInterface $applicationManager
     * @param DeviceManagerInterface      $deviceManager
     * @param UserManagerInterface        $userManager
     * @param TagManagerInterface         $tagManager
     * @param string                      $uidAutoPrefix
     */
    public function __construct(
        ApplicationManagerInterface $applicationManager,
        DeviceManagerInterface $deviceManager,
        UserManagerInterface $userManager,
        TagManagerInterface $tagManager,
        $uidAutoPrefix = 'op_'
    ) {
        $this->applicationManager = $applicationManager;
        $this->deviceManager = $deviceManager;
        $this->userManager = $userManager;
        $this->tagManager = $tagManager;
        $this->uidAutoPrefix = $uidAutoPrefix;
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
     * @param string $applicationId
     * @param string $deviceIdentifier
     * @param string $token
     * @param string $uid
     * @param float  $locationLatitude
     * @param float  $locationLongitude
     * @param string $userAgent
     * @param int    $type
     * @param string $key
     * @param string $auth
     *
     * @throws ApplicationNotFoundException
     *
     * @return array
     */
    public function registerDevice(
        $applicationId,
        $deviceIdentifier,
        $token,
        $uid,
        $locationLatitude,
        $locationLongitude,
        $userAgent,
        $type,
        $key = null,
        $auth = null
    ) {
        $application = $this->getApplicaiton($applicationId);

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
        $device->setUserAgent($userAgent);
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
                if (empty($uid)) {
                    $uid = uniqid($this->uidAutoPrefix);
                }
            }
        }

        $user->setApplication($application);
        $user->setUid($uid);

        $device->setUser($user);
        $user->addDevice($device);

        $application->addUser($user);

        $tags = $this->tagManager->getTagObjects([
            Device::getTypeName($device->getType()),
            $device->getUser()->getUidTag(),
        ]);

        foreach ($tags as $tag) {
            $user->addTag($tag);
        }

        $this->deviceManager->save($device);

        return [
            'deviceIdentifier' => $deviceIdentifier,
            'uid' => $uid,
            'tags' => $user->getTagNames()->toArray(),
            'registrationDate' => $device->getRegisteredAt(),
        ];
    }

    /**
     * Unregisters a device from the application.
     *
     * @param string $applicationId
     * @param string $deviceIdentifier
     * @param bool   $deletion
     *
     * @throws ApplicationNotFoundException
     *
     * @return array
     */
    public function unregisterDevice($applicationId, $deviceIdentifier, $deletion = false)
    {
        $application = $this->getApplicaiton($applicationId);

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (!is_null($device)) {
            $result = [
                'deviceIdentifier' => $deviceIdentifier,
                'uid' => $device->getUser()->getUid(),
            ];
            if ($deletion) {
                $this->deviceManager->delete($device);

                return $result;
            }

            $device->setUnregisteredAt(new \DateTime());
            $device->getUser()->setBadge(0);
            $this->deviceManager->save($device);

            $result['unregistrationDate'] = $device->getUnregisteredAt();

            return $result;
        }

        return ['message' => 'No device.'];
    }

    /**
     * Get registration information.
     *
     * @param string $applicationId
     * @param string $deviceIdentifier
     *
     * @throws ApplicationNotFoundException
     * @throws DeviceNotFoundException
     *
     * @return array
     */
    public function getRegistration($applicationId, $deviceIdentifier)
    {
        $application = $this->getApplicaiton($applicationId);

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (!is_null($device)) {
            return [
                'deviceIdentifier' => $deviceIdentifier,
                'uid' => $device->getUser()->getUid(),
                'tags' => $device->getUser()->getTagNames()->toArray(),
                'registrationDate' => $device->getRegisteredAt(),
            ];
        }

        throw new DeviceNotFoundException(sprintf('Device %s is not found.', $deviceIdentifier));
    }

    /**
     * @param string $applicationId
     *
     * @throws ApplicationNotFoundException
     *
     * @return \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected function getApplicaiton($applicationId)
    {
        $application = $this->applicationManager->findApplicationBy(['slug' => $applicationId]);

        if (is_null($application)) {
            throw new ApplicationNotFoundException(sprintf('Application %s is not found.', $applicationId));
        }

        return $application;
    }
}
