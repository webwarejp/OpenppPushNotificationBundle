<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * ApplicationManagerInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface ApplicationManagerInterface
{
    /**
     * Finds a application by its package name.
     *
     * @param string $packagename
     *
     * @return ApplicationInterface or null
     */
    public function findApplicationByPackageName($packagename);

    /**
     * Finds one application by the given criteria.
     *
     * @param array $criteria
     *
     * @return ApplicationInterface or null
     */
    public function findApplicationBy(array $criteria);

    /**
     * Returns the application's fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}