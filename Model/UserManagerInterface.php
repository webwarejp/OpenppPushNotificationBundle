<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * UserManagerInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface UserManagerInterface
{
    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Finds a user by its uid and application.
     *
     * @param ApplicationInterface $application
     * @param string $uid
     *
     * @return UserInterface or null
     */
    public function findUserByUid(ApplicationInterface $application, $uid);

    /**
     * Finds one user by the given criteria.
     *
     * @param array $criteria
     *
     * @return UserInterface or null
     */
    public function findUserBy(array $criteria);

    /**
     * Returns an empty user instance
     *
     * @return UserInterface
     */
    public function create();

    /**
     * Saves a user.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function save(UserInterface $user, $andFlush = true);

    /**
     * Adds the tag(s) to user.
     *
     * @param ApplicationInterface $application
     * @param string $uid
     * @param TagInterface|array $tags
     * @param boolean $andFlush
     */
    public function addTagToUser(ApplicationInterface $application, $uid, $tags, $andFlush = true);

    /**
     * Removes the tag(s) from user.
     *
     * @param ApplicationInterface $application
     * @param string $uid
     * @param TagInterface|array $tags
     * @param boolean $andFlush
     */
    public function removeTagFromUser(ApplicationInterface $application, $uid, $tags, $andFlush = true);

    /**
     * Returns whether the user having the tag exists.
     *
     * @param ApplicationInterface $application
     * @param string $target                    Tag or Tag Expressions
     * @param integer $type                     DeviceInterface::TYPE_ANDROID|DeviceInterface::TYPE_IOS
     */
    public function hasUserWithTag(ApplicationInterface $application, $target, $type);
}