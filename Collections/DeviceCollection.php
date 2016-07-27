<?php

namespace Openpp\PushNotificationBundle\Collections;

use Doctrine\Common\Collections\ArrayCollection;

class DeviceCollection extends ArrayCollection
{
    /**
     * @param integer $type
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getByType($type)
    {
        return $this->filter(function ($d) use ($type) {
            return $d->getType() == $type;
        });
    }

    /**
     * @param integer $type
     * @return integer
     */
    public function countByType($type)
    {
        return $this->getByType($type)->count();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function toIdArray()
    {
        return $this->map(function($d) {
            return $d->getId();
        });
    }
}