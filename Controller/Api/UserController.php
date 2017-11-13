<?php

namespace Openpp\PushNotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\PushNotificationBundle\Exception\UserNotFoundException;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;

class UserController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="Get a paginated list of uids",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Get("/{version}/user/uids", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @QueryParam(name="application_id", description="Application ID", strict=true)
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for uids list pagination (1-indexed)", strict=false)
     * @QueryParam(name="count", requirements="\d+", default="1024", description="Number of uids by page", strict=false)
     */
    public function getUidsAction(ParamFetcherInterface $paramFetcher)
    {
        $application = $this->getApplication($paramFetcher->get('application_id'));
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');

        $pager = $this->getUserManager()->getPager(['application' => $application], $page, $limit);

        $uids = [];
        foreach ($pager->getResults() as $user) {
            $uids[] = $user->getUid();
        }

        return [
            'page' => $pager->getPage(),
            'last_page' => $pager->getLastPage(),
            'per_page' => $limit,
            'total' => $pager->getNbResults(),
            'uids' => $uids,
        ];
    }

    /**
     * @ApiDoc(
     *  description="Add tags to user",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Post("/{version}/user/tags/add", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="tags", description="Array of tags to add", strict=true)
     */
    public function addUserTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $application = $this->getApplication($paramFetcher->get('application_id'));
        $user = $this->getApplicationUser($application, $paramFetcher->get('uid'));
        $tags = $paramFetcher->get('tags');
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $this->get('openpp.push_notification.push_service_manager')->addTagToUser(
             $paramFetcher->get('application_id'), $paramFetcher->get('uid'), $tags
        );

        $result = [
            'application_id' => $application->getSlug(),
            'uid' => $user->getUid(),
        ];
        $result['tags'] = array_unique(array_merge($user->getTagNames()->toArray(), $tags));

        return $result;
    }

    /**
     * @ApiDoc(array
     *  description="Remove tags from user",
     *  section="Openpp Push Notifications (Common)"
     * )
     *
     * @Post("/{version}/user/tags/remove", requirements={"version" = "v1"}, defaults={"_format"="json"})
     * @RequestParam(name="application_id", description="Application ID", strict=true)
     * @RequestParam(name="uid", description="User identifier", strict=true)
     * @RequestParam(name="tags", description="Array of tags to remove", strict=true)
     */
    public function removeUserTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $application = $this->getApplication($paramFetcher->get('application_id'));
        $user = $this->getApplicationUser($application, $paramFetcher->get('uid'));
        $tags = $paramFetcher->get('tags');
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        $this->get('openpp.push_notification.push_service_manager')->removeTagFromUser(
             $paramFetcher->get('application_id'), $paramFetcher->get('uid'), $tags
        );

        $result = [
            'application_id' => $application->getSlug(),
            'uid' => $user->getUid(),
        ];
        $result['tags'] = array_diff($user->getTagNames()->toArray(), $tags);

        return $result;
    }

    /**
     * Get an application.
     *
     * @param string $aid
     *
     * @throws ApplicationNotFoundException
     *
     * @return \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected function getApplication($aid)
    {
        $applicationManager = $this->get('openpp.push_notification.manager.application');
        $application = $applicationManager->findApplicationBy(['slug' => $aid]);
        if (empty($application)) {
            throw new ApplicationNotFoundException(sprintf('Application %s is not found.', $aid));
        }

        return $application;
    }

    /**
     * Get a applicaiton user.
     *
     * @param ApplicationInterface $application
     * @param string               $uid
     *
     * @throws UserNotFoundException
     *
     * @return \Openpp\PushNotificationBundle\Model\UserInterface
     */
    protected function getApplicationUser(ApplicationInterface $application, $uid)
    {
        $user = $this->getUserManager()->findUserByUid($application, $uid);
        if (empty($user)) {
            throw new UserNotFoundException(sprintf('User %s is not found.', $uid));
        }

        return $user;
    }

    /**
     * Get the user manager.
     *
     * @return \Openpp\PushNotificationBundle\Model\UserManagerInterface
     */
    protected function getUserManager()
    {
        return $this->get('openpp.push_notification.manager.user');
    }
}
