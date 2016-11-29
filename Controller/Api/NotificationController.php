<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;

/**
 *
 */
class NotificationController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Send the push notification",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Post("/{version}/notification/send", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="title", description="Title of the push notification", strict=false)
     * @RequestParam(name="message", description="Message to be displayed in the push notification", strict=true)
     * @RequestParam(name="icon", description="URL of the icon image to be shown in the notification", strict=false)
     * @RequestParam(name="url", description="URL to open upon clicking of the push notification", strict=false)
     * @RequestParam(name="tag_exp", description="Tag expression to select users to send the notification", strict=false)
     */
    public function sendAction(ParamFetcherInterface $paramFetcher)
    {
        $applicationId = $paramFetcher->get('application_id');
        $applicationManager = $this->get('openpp.push_notification.manager.application');
        if (!$applicationManager->findApplicationBy(array('slug' => $applicationId))) {
            throw new ApplicationNotFoundException(sprintf('Application %s is not found.', $applicationId));
        }

        $options = array();
        if ($title = $paramFetcher->get('title')) {
            $options['title'] = $title;
        }
        if ($icon = $paramFetcher->get('icon')) {
            $options['icon'] = $icon;
        }
        if ($url = $paramFetcher->get('url')) {
            $options['url'] = $url;
        }

        $this->get('openpp.push_notification.push_service_manager')->push(
             $applicationId, $paramFetcher->get('tag_exp'), $paramFetcher->get('message'), $options
        );

        return array('result' => true);
    }
}