<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Condition as ModelCondition;

abstract class BaseCondition extends ModelCondition
{
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
        $this->setTimeCondition();
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
        $this->setTimeCondition();
    }

    protected function setTimeCondition()
    {
        if (!$this->getTimeType()) {
            $this->setSpecificDates(array());
            $this->setStartDate(null);
            $this->setEndDate(null);
            $this->setIntervalType(null);
            $this->setIntervalTime(null);
        } else if ($this->getTimeType() === self::TIME_TYPE_SPECIFIC) {
            $this->setStartDate(null);
            $this->setEndDate(null);
            $this->setIntervalType(null);
            $this->setIntervalTime(null);
        } else if ($this->getTimeType() === self::TIME_TYPE_PERIODIC) {
            $this->setSpecificDates(array());
        }
    }
}