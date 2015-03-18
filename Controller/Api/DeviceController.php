<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

class DeviceController
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
     * Constructor
     *
     * @param ApplicationManagerInterface $applicationManager
     * @param DeviceManagerInterface $deviceManager
     * @param UserManagerInterface $userManager
     */
    public function __construct(ApplicationManagerInterface $applicationManager, DeviceManagerInterface $deviceManager, UserManagerInterface $userManager)
    {
        $this->applicationManager = $applicationManager;
        $this->deviceManager      = $deviceManager;
        $this->userManager        = $userManager;
    }

    /**
     * @ApiDoc(
     *  description="Registers an Android Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Rest\View()
     *
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     * @RequestParam(name="registration_id", description="The registration id returned from GCM", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     */
    public function registerAndroidDeviceAction(ParamFetcherInterface $paramFetcher)
    {
        $applicationName  = $paramFetcher->get('application_name');
        $deviceIdentifier = $paramFetcher->get('device_identifier');
        $registrationId   = $paramFetcher->get('registration_id');
        $uid              = $paramFetcher->get('uid');

        return registerDevice($applicationName, $deviceIdentifier, $registrationId, $uid, DeviceInterface::TYPE_ANDROID);
    }

    /**
     * @ApiDoc(
     *  description="Registers an iOS Device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Rest\View()
     *
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     * @RequestParam(name="device_token", description="The device token returned from Apple.", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     */
    public function registeriOSDeviceAction(ParamFetcherInterface $paramFetcher)
    {
        $applicationName  = $paramFetcher->get('application_name');
        $deviceIdentifier = $paramFetcher->get('device_identifier');
        $deviceToken      = $paramFetcher->get('device_token');
        $uid              = $paramFetcher->get('uid');

        return $this->registerDevice($applicationName, $deviceIdentifier, $deviceToken, $uid, DeviceInterface::TYPE_IOS);
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an Android GCM Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Rest\View()
     *
     * @RequestParam(name="application_name", description="The name of the application unregistering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     */
    public function unregisterAndroidDeviceAction(ParamFetcherInterface $paramFetcher)
    {
        $applicationName = $paramFetcher->get('application_name');
        $deviceIdentifier = $paramFetcher->get('device_identifier');

        return $this->unregisterDevice($applicationName, $deviceIdentifier);
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an iOS Device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Rest\View()
     *
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     */
    public function unregisteriOSDeviceAction(ParamFetcherInterface $paramFetcher)
    {
        $applicationName = $paramFetcher->get('application_name');
        $deviceIdentifier = $paramFetcher->get('device_identifier');

        return $this->unregisterDevice($applicationName, $deviceIdentifier);
    }

    /**
     * Registers a device to the application.
     *
     * @param string  $applicationName
     * @param string  $deviceIdentifier
     * @param string  $token
     * @param string  $uid
     * @param integer $type
     *
     * @return NULL
     */
    protected function registerDevice($applicationName, $deviceIdentifier, $token, $uid, $type)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);

        if (is_null($application)) {
            return null;
        }

        $user = $this->userManager->findUserByUid($application, $uid);

        if (is_null($user)) {
            $user = $this->userManager->createUser();
        }

        $device = $user->getDeviceByIdentifier($deviceIdentifier);

        if (is_null($device)) {
            $device = $this->deviceManager->createDevice();
        }

        $device->setApplication($application);
        $device->setDeviceIdentifier($deviceIdentifier);
        $device->setToken($token);
        $device->setType($type);
        $device->setRegisteredAt(new \Datetime());
        $device->setUnregisteredAt(null);

        $user->setApplication($application);
        $user->setUid($uid);

        $device->setUser($user);
        $user->addDevice($device);
        $application->addUser($user);

        $this->deviceManager->updateDevice($device);

        return null;
    }

    /**
     * Unregisters a device from the application.
     *
     * @param string $applicationName
     * @param string $deviceIdentifier
     *
     * @return NULL
     */
    protected function unregisterDevice($applicationName, $deviceIdentifier)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);

        if (is_null($application)) {
            return null;
        }

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (!is_null($device)) {
            $device->setUnregisteredAt(new \Datetime());
            $device->getUser()->setBadge(0);
            $this->deviceManager->updateDevice($device);
        }

        return null;
    }
}