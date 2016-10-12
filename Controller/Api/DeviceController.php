<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

/**
 *
 * @author shiroko@webware.co.jp
 *
 */
class DeviceController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Registers an Android Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/device/android/register", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to register.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     * @RequestParam(name="registration_id", description="The registration id returned from GCM", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     * @RequestParam(name="location_latitude", description="The latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="The longitude of device's location", strict=false)
     */
    public function registerDeviceAndroidAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->registerDevice(
            $paramFetcher->get('application_id'),
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
     * @Post("/device/ios/register", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to register.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     * @RequestParam(name="device_token", description="The device token returned from Apple.", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=true)
     * @RequestParam(name="location_latitude", description="The latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="The longitude of device's location", strict=false)
     */
    public function registerDeviceIosAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->registerDevice(
            $paramFetcher->get('application_id'),
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
     *  description="Registers an Web browser",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/device/web/register", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to register.", strict=true)
     * @RequestParam(name="endpoint", description="The URL that allows an application server to request delivery of a push message to a webapp.", strict=true)
     * @RequestParam(name="key", description="The keying material used to encrypt push messages.", strict=true)
     * @RequestParam(name="auth", description="keying material used to authenticate push messages.", strict=true)
     * @RequestParam(name="uid", description="The user identifier", strict=false)
     * @RequestParam(name="location_latitude", description="The latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="The longitude of device's location", strict=false)
     */
    public function registerDeviceWebAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->registerDevice(
                $paramFetcher->get('application_id'),
                $paramFetcher->get('endpoint'),
                $paramFetcher->get('endpoint'),
                $paramFetcher->get('uid'),
                $paramFetcher->get('location_latitude'),
                $paramFetcher->get('location_longitude'),
                DeviceInterface::TYPE_WEB,
                $paramFetcher->get('key'),
                $paramFetcher->get('auth')
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an Android GCM Device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/device/android/unregister", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to unregister.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the Android device.", strict=true)
     */
    public function unregisterDeviceAndroidAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->unregisterDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('device_identifier')
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an iOS Device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Post("/device/ios/unregister", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to unregister.", strict=true)
     * @RequestParam(name="device_identifier", description="The vendor device identifier of the iOS device.", strict=true)
     */
    public function unregisterDeviceIosAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->unregisterDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('device_identifier')
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregisters an Web browser",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/device/web/unregister", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="The application ID to unregister.", strict=true)
     * @RequestParam(name="endpoint", description="The URL that allows an application server to request delivery of a push message to a webapp.", strict=true)
     */
    public function unregisterDeviceWebAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->unregisterDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('endpoint')
        );
    }

    /**
     * @return object
     */
    protected function getManipurator()
    {
        return $this->get('openpp.push_notification.manipurator.register');
    }
}
