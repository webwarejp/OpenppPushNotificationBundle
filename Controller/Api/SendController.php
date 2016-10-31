<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SendController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description=".",
     *  section="Openpp Push Notifications"
     * )
     *
     * @Post("/send/all", defaults={"_format"="json"})
     * @RequestParam(name="application", description="The application ID to register.", strict=true)
     * @RequestParam(name="title", description="The application ID to register.", strict=false)
     * @RequestParam(name="message", description="The application ID to register.", strict=true)
     * @RequestParam(name="icon", description="The application ID to register.", strict=false)
     * @RequestParam(name="url", description="The application ID to register.", strict=false)
     */
    public function sendAction(ParamFetcherInterface $paramFetcher)
    {
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
             $paramFetcher->get('application'), null, $paramFetcher->get('message'), $options
        );

        return array('result' => true);
    }
}