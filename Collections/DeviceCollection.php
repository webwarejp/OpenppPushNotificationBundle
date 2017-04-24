<?php

namespace Openpp\PushNotificationBundle\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

class DeviceCollection extends ArrayCollection
{
    /**
     * @param integer $type
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getByType($type)
    {
        return $this->filter(function (DeviceInterface $d) use ($type) {
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
        return $this->map(function(DeviceInterface $d) {
            return $d->getId();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function sortByField($field)
    {
        $criteria = Criteria::create()->orderBy(array($field => Criteria::ASC));

        return $this->matching($criteria);
    }
}
