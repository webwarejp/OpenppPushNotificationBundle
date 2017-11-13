<?php

namespace Openpp\PushNotificationBundle\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

class DeviceCollection extends ArrayCollection
{
    /**
     * Returns the specified type devices.
     *
     * @param int $type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getByType($type)
    {
        return $this->filter(function (DeviceInterface $d) use ($type) {
            return $d->getType() == $type;
        });
    }

    /**
     * Counts the devices of specified type.
     *
     * @param int $type
     *
     * @return int
     */
    public function countByType($type)
    {
        return $this->getByType($type)->count();
    }

    /**
     * Returns the array of all devices identifier.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function toIdArray()
    {
        return $this->map(function (DeviceInterface $d) {
            return $d->getId();
        });
    }

    /**
     * Returns the array of devices that sorted by specified field.
     *
     * @param string $field
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function sortByField($field)
    {
        $criteria = Criteria::create()->orderBy([$field => Criteria::ASC]);

        return $this->matching($criteria);
    }
}
