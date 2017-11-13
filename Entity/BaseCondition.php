<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Condition as ModelCondition;

abstract class BaseCondition extends ModelCondition
{
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setTimeCondition();
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
        $this->setTimeCondition();
    }

    protected function setTimeCondition()
    {
        if (!$this->getTimeType()) {
            $this->setSpecificDates([]);
            $this->setStartDate(null);
            $this->setEndDate(null);
            $this->setIntervalType(null);
            $this->setIntervalTime(null);
        } elseif (self::TIME_TYPE_SPECIFIC === $this->getTimeType()) {
            $this->setStartDate(null);
            $this->setEndDate(null);
            $this->setIntervalType(null);
            $this->setIntervalTime(null);
            sort($this->specificDates);
        } elseif (self::TIME_TYPE_PERIODIC === $this->getTimeType()) {
            $this->setSpecificDates([]);
        }
    }
}
