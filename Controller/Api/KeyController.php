<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Base64Url\Base64Url;
use Openpp\WebPushAdapter\Util\PublicKeyUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KeyController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Retrieve the server public key for web push.",
     *  section="Openpp Push Notifications (Web Push)"
     * )
     *
     * @Get("/key/publicKey", defaults={"_format"="json"})
     */
    public function getPublicKeyAction()
    {
        if ($path = $this->getParameter('openpp.push_notification.web_push.public_key_path')) {

            return new Response(Base64Url::encode(PublicKeyUtil::getKeyFromPem($path)));
        }

        throw new NotFoundHttpException('No public key.');
    }
}