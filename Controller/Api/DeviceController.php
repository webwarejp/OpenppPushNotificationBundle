<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Symfony\Component\HttpFoundation\Request;

class DeviceController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Register an Android device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/{version}/device/android/register", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="device_identifier", description="Vendor device identifier of the Android device", strict=true)
     * @RequestParam(name="registration_id", description="Registration id returned from GCM", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="location_latitude", description="Latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="Longitude of device's location", strict=false)
     */
    public function registerDeviceAndroidAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->getManipurator()->registerDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('device_identifier'),
            $paramFetcher->get('registration_id'),
            $paramFetcher->get('uid'),
            $paramFetcher->get('location_latitude'),
            $paramFetcher->get('location_longitude'),
            $request->server->get('HTTP_USER_AGENT'),
            DeviceInterface::TYPE_ANDROID
        );
    }

    /**
     * @ApiDoc(
     *  description="Register an iOS device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Post("/{version}/device/ios/register", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="device_identifier", description="Vendor device identifier of the iOS device", strict=true)
     * @RequestParam(name="device_token", description="Device token returned from Apple", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="location_latitude", description="Latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="Longitude of device's location", strict=false)
     */
    public function registerDeviceIosAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->getManipurator()->registerDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('device_identifier'),
            $paramFetcher->get('device_token'),
            $paramFetcher->get('uid'),
            $paramFetcher->get('location_latitude'),
            $paramFetcher->get('location_longitude'),
            $request->server->get('HTTP_USER_AGENT'),
            DeviceInterface::TYPE_IOS
        );
    }

    /**
     * @ApiDoc(
     *  description="Register an Web Browser",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/{version}/device/web/register", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="endpoint", description="URL that allows an application server to request delivery of a push message to a webapp", strict=true)
     * @RequestParam(name="key", description="Keying material used to encrypt push messages", strict=true)
     * @RequestParam(name="auth", description="Keying material used to authenticate push messages", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=false)
     * @RequestParam(name="location_latitude", description="Latitude of device's location", strict=false)
     * @RequestParam(name="location_longitude", description="Longitude of device's location", strict=false)
     */
    public function registerDeviceWebAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->getManipurator()->registerDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('endpoint'),
            $paramFetcher->get('endpoint'),
            $paramFetcher->get('uid'),
            $paramFetcher->get('location_latitude'),
            $paramFetcher->get('location_longitude'),
            $request->server->get('HTTP_USER_AGENT'),
            DeviceInterface::TYPE_WEB,
            $paramFetcher->get('key'),
            $paramFetcher->get('auth')
        );
    }

    /**
     * @ApiDoc(
     *  description="Unregister an Android device",
     *  section="Openpp Push Notifications (GCM)"
     * )
     *
     * @Post("/{version}/device/android/unregister", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="device_identifier", description="Vendor device identifier of the Android device", strict=true)
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
     *  description="Unregister an iOS device",
     *  section="Openpp Push Notifications (iOS)"
     * )
     *
     * @Post("/{version}/device/ios/unregister", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="device_identifier", description="Vendor device identifier of the iOS device", strict=true)
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
     *  description="Unregister a Web Browser",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/{version}/device/web/unregister", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="endpoint", description="URL that allows an application server to request delivery of a push message to a webapp", strict=true)
     */
    public function unregisterDeviceWebAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->unregisterDevice(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('endpoint'),
            true
        );
    }

    /**
     * @ApiDoc(
     *  description="Get the registration information for a given devce",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Get("/{version}/device/registration", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @QueryParam(name="application_id", description="Application ID", strict=true)
     * @QueryParam(name="device_identifier", description="Device identifier", strict=true)
     */
    public function getRegistrationAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->getManipurator()->getRegistration(
            $paramFetcher->get('application_id'),
            $paramFetcher->get('device_identifier')
        );
    }

    /**
     * Returns the registration manipurator.
     *
     * @return object
     */
    protected function getManipurator()
    {
        return $this->get('openpp.push_notification.manipurator.register');
    }
}
