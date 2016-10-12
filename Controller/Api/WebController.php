<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class WebController extends FOSRestController
{
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
        $history = $this->getHistoryManager()->findHistoryBy(array('notificationId' => $paramFetcher->get('tag')));
        if (!empty($history)) {
            $history->setClickCount($history->getClickCount() + 1);
            $this->getHistoryManager()->save($history);
        } else {
            throw new \Exception('tag: ' . $paramFetcher->get('tag'));
        }

        return array('result' => true);
    }

    protected function getHistoryManager()
    {
        return $this->get('openpp.push_notification.manager.history');
    }
}