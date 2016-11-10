<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Exception\UserNotFoundException;

class UserController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Add tags to user",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Post("/user/tags/add", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="tags", array=true, description="Array of tags to add", strict=true)
     */
    public function addUserTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $application = $this->getApplication($paramFetcher->get('application_id'));
        $user = $this->getApplicationUser($application, $paramFetcher->get('uid'));

        $this->get('openpp.push_notification.push_service_manager')->addTagToUser(
             $paramFetcher->get('application_id'), $paramFetcher->get('uid'), $paramFetcher->get('tags')
        );

        $result = array(
            'application_id' => $application->getSlug(),
            'uid'            => $user->getUid(),
        );
        $result['tags'] = array_unique(array_merge($user->getTagNames()->toArray(), $paramFetcher->get('tags')));

        return $result;
    }

    /**
     * @ApiDoc(
     *  description="Remove tags from user",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Post("/user/tags/remove", defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="tags", array=true, description="Array of tags to remove", strict=true)
     */
    public function removeUserTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $application = $this->getApplication($paramFetcher->get('application_id'));
        $user = $this->getApplicationUser($application, $paramFetcher->get('uid'));

        $this->get('openpp.push_notification.push_service_manager')->removeTagFromUser(
             $paramFetcher->get('application_id'), $paramFetcher->get('uid'), $paramFetcher->get('tags')
        );

        $result = array(
            'application_id' => $application->getSlug(),
            'uid'            => $user->getUid(),
        );
        $result['tags'] = array_diff($user->getTagNames()->toArray(), $paramFetcher->get('tags'));

        return $result;
    }

    /**
     * Get application
     *
     * @param string $aid
     * @throws ApplicationNotFoundException
     * @return \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected function getApplication($aid)
    {
        $applicationManager = $this->get('openpp.push_notification.manager.application');
        $application = $applicationManager->findApplicationBy(array('slug' => $aid));
        if (empty($application)) {
            throw new ApplicationNotFoundException(sprintf('Application %s is not found.', $aid));
        }

        return $application;
    }

    /**
     * Get applicaiton user
     *
     * @param ApplicationInterface $application
     * @param string $uid
     *
     * @throws UserNotFoundException
     * @return \Openpp\PushNotificationBundle\Model\UserInterface
     */
    protected function getApplicationUser(ApplicationInterface $application, $uid)
    {
        $userManager = $this->get('openpp.push_notification.manager.user');
        $user = $userManager->findUserByUid($application, $uid);
        if (empty($user)) {
            throw new UserNotFoundException(sprintf('User %s is not found.', $uid));
        }

        return $user;
    }
}