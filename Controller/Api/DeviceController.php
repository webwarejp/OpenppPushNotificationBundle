<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;


/**
 *
 * @author shiroko@webware.co.jp
 *
 */
class DeviceController extends FOSRestController
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
     * Constructor
     *
     * @param ApplicationManagerInterface $applicationManager
     * @param DeviceManagerInterface $deviceManager
     * @param UserManagerInterface $userManager
     * @param TagManagerInterface $tagManager
     */
    public function __construct(ApplicationManagerInterface $applicationManager, DeviceManagerInterface $deviceManager, UserManagerInterface $userManager, TagManagerInterface $tagManager)
    {
        $this->applicationManager = $applicationManager;
        $this->deviceManager      = $deviceManager;
        $this->userManager        = $userManager;
        $this->tagManager         = $tagManager;
    }

    /**
     * @ApiDoc(
     *  description="Registers an Android Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/device/android/register", defaults={"_format"="json"})
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     * @RequestParam(name="registration_id", description="The registration id returned from GCM", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     * @RequestParam(name="location_latitude", description="The latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="The longitude of device's location", strict=false)
     */
    public function registerDeviceAndroidAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->registerDevice(
            $paramFetcher->get('application_name'),
            $paramFetcher->get('device_identifier'),
            $paramFetcher->get('registration_id'),
            $paramFetcher->get('uid'),
            $paramFetcher->get('location_latitude'),
            $paramFetcher->get('location_longitude'),
            DeviceInterface::TYPE_ANDROID
        );
    }

    /**
     * @ApiDoc(
     *  description="Registers an iOS Device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Post("/device/ios/register", requirements={"_format"="json"})
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     * @RequestParam(name="device_token", description="The device token returned from Apple.", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     * @RequestParam(name="location_latitude", description="The latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="The longitude of device's location", strict=false)
     */
    public function registerDeviceIosAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->registerDevice(
            $paramFetcher->get('application_name'),
            $paramFetcher->get('device_identifier'),
            $paramFetcher->get('device_token'),
            $paramFetcher->get('uid'),
            $paramFetcher->get('location_latitude'),
            $paramFetcher->get('location_longitude'),
            DeviceInterface::TYPE_IOS
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an Android GCM Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/device/android/unregister", requirements={"_format"="json"})
     * @RequestParam(name="application_name", description="The name of the application unregistering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     */
    public function unregisterDeviceAndroidAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->unregisterDevice(
            $paramFetcher->get('application_name'),
            $paramFetcher->get('device_identifier')
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an iOS Device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Post("/device/ios/register", requirements={"_format"="json"})
     * @RequestParam(name="application_name", description="The name of the application registering.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     */
    public function unregisterDeviceIosAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->unregisterDevice(
            $paramFetcher->get('application_name'),
            $paramFetcher->get('device_identifier')
        );
    }

    /**
     * Registers a device to the application.
     *
     * @param string  $applicationName
     * @param string  $deviceIdentifier
     * @param string  $token
     * @param string  $uid
     * @param integer $type
     * @param float   $locationLatitude
     * @param float   $locationLongitude
     *
     * @throws ApplicationNotFoundException
     *
     * @return multitype:boolean
     */
    protected function registerDevice($applicationName, $deviceIdentifier, $token, $uid, $locationLatitude, $locationLongitude, $type)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);

        if (is_null($application)) {
            throw new ApplicationNotFoundException('Application ' . $applicationName . ' is not found.');
        }

        $user = $this->userManager->findUserByUid($application, $uid);

        if (is_null($user)) {
            $user = $this->userManager->create();
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
                try {
                    $pointManager = $this->get('openpp.map.manager.point');
                    $device->setLocation($pointManager->createFromLonLat($locationLongitude, $locationLatitude));
                } catch (ServiceNotFoundException $e) {
                    // do nothing
                }
            }
        }

        $user->setApplication($application);
        $user->setUid($uid);

        $device->setUser($user);
        $user->addDevice($device);

        $tagNames = array();
        if ($type == DeviceInterface::TYPE_ANDROID) {
            $tagNames[] = 'android';
        } else {
            $tagNames[] = 'ios';
        }
        $tagNames[] = $user->getUidTag();

        foreach ($tagNames as $tagName) {
            $tag = $this->tagManager->findTagByName($tagName);

            if (is_null($tag)) {
                $tag = $this->tagManager->create();
                $tag->setName($tagName);
            }
            $user->addTag($tag);
        }

        $application->addUser($user);

        $this->deviceManager->save($device);

        return array('registered' => true);
    }

    /**
     * Unregisters a device from the application.
     *
     * @param string $applicationName
     * @param string $deviceIdentifier
     *
     * @throws ApplicationNotFoundException
     *
     * @return multitype:boolean
     */
    protected function unregisterDevice($applicationName, $deviceIdentifier)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);

        if (is_null($application)) {
            throw new ApplicationNotFoundException('Application ' . $applicationName . ' is not found.');
        }

        $device = $this->deviceManager->findDeviceByIdentifier($application, $deviceIdentifier);

        if (!is_null($device)) {
            $device->setUnregisteredAt(new \DateTime());
            $device->getUser()->setBadge(0);
            $this->deviceManager->save($device);
        }

        return array('unregistered' => true);
    }
}
