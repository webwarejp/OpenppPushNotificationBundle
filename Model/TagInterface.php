<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * TagInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface TagInterface
{
    const UID_TAG_PREFIX = 'uid_';

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function setName($name);
}
