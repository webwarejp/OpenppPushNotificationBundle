<?php

namespace Openpp\PushNotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OpenppPushNotificationBundle:Default:index.html.twig', array('name' => $name));
    }
}
