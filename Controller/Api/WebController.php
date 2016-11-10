<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Consumer\TrackConsumer;

class WebController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Record the notification delivered",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/web/track-delivered", defaults={"_format"="json"})
     * @RequestParam(name="tag", description="Notification ID", strict=true)
     * @RequestParam(name="subscription_id", description="Subscription ID (endpoint)", strict=true)
     */
    public function trackDeliveredAction(ParamFetcherInterface $paramFetcher)
    {
        $this->get('sonata.notification.backend')->createAndPublish(
            TrackConsumer::TYPE_NAME,
            array(
                'operation' => TrackConsumer::OPERATION_DELIVERED,
                'notificationId' => $paramFetcher->get('tag'),
                'subscriptionId' => $paramFetcher->get('subscription_id'),
            )
        );

        return array('result' => true);
    }

    /**
     * @ApiDoc(
     *  description="Record the notification clicks",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Post("/web/track-click", defaults={"_format"="json"})
     * @RequestParam(name="tag", description="Notification ID", strict=true)
     * @RequestParam(name="subscription_id", description="Subscription ID (endpoint)", strict=true)
     */
    public function trackClickAction(ParamFetcherInterface $paramFetcher)
    {
        $this->get('sonata.notification.backend')->createAndPublish(
            TrackConsumer::TYPE_NAME,
            array(
                'operation' => TrackConsumer::OPERATION_CLICK,
                'notificationId' => $paramFetcher->get('tag'),
                'subscriptionId' => $paramFetcher->get('subscription_id'),
            )
        );

        return array('result' => true);
    }
}