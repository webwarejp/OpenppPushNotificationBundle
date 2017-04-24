<?php

namespace Openpp\PushNotificationBundle\Model;

interface ConditionManagerInterface
{
    /**
     * Returns the Entity class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Returns the continuing conditions.
     *
     * @return ConditionInterface[]
     */
    public function getContinuingConditions();
}
