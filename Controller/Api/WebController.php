<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Record the notification delivered",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/web/track-delivered", defaults={"_format"="json"})
     * @RequestParam(name="tag", description="The notification ID.", strict=true)
     * @RequestParam(name="subscription_id", description="The subscription ID.", strict=true)
     */
    public function trackDeliveredAction(ParamFetcherInterface $paramFetcher)
    {
        $device = $this->findDevice($paramFetcher->get('subscription_id'));
        if (empty($device)) {
            throw new NotFoundHttpException(sprintf('No device(%s) found.', $paramFetcher->get('subscription_id')));
        }
        $device->setLastDeliveredNotificationId($paramFetcher->get('tag'));
        $this->getDeviceManager()->save($device);

        $history = $this->findHistory($paramFetcher->get('tag'));
        if (empty($history)) {
            throw new NotFoundHttpException(sprintf('No history(%s) found.', $paramFetcher->get('tag')));
        }
        $history->setDeliveredCount($history->getDeliveredCount() + 1);
        $this->getHistoryManager()->save($history);

        return array('result' => true);
    }

    /**
     * @ApiDoc(
     *  description="Record the notification clicks",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/web/track-click", defaults={"_format"="json"})
     * @RequestParam(name="tag", description="The notification ID.", strict=true)
     * @RequestParam(name="subscription_id", description="The subscription ID.", strict=true)
     */
    public function trackClickAction(ParamFetcherInterface $paramFetcher)
    {
        $history = $this->findHistory($paramFetcher->get('tag'));
        if (empty($history)) {
            throw new NotFoundHttpException(sprintf('No history(%s) found.', $paramFetcher->get('tag')));
        }
        $history->setClickCount($history->getClickCount() + 1);
        $this->getHistoryManager()->save($history);

        return array('result' => true);
    }

    /**
     * @param string $subscriptionId
     * @return \Openpp\PushNotificationBundle\Model\DeviceInterface
     */
    protected function findDevice($subscriptionId)
    {
        return $this->getDeviceManager()->findDeviceBy(array('token' => $subscriptionId));
    }

    /**
     * @param string $notificationId
     * @return \Openpp\PushNotificationBundle\Model\HistoryInterface
     */
    protected function findHistory($notificationId)
    {
        return $this->getHistoryManager()->findHistoryBy(array('notificationId' => $notificationId));
    }

    /**
     * @return object
     */
    protected function getDeviceManager()
    {
        return $this->get('openpp.push_notification.manager.device');
    }

    /**
     * @return object
     */
    protected function getHistoryManager()
    {
        return $this->get('openpp.push_notification.manager.history');
    }
}