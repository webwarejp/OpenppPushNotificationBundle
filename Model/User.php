<?php

namespace Openpp\PushNotificationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class User implements UserInterface
{
    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var ArrayCollection
     */
    protected $devices;

    /**
     * @var ArrayCollection
     */
    protected $tags;

    /**
     * @var int
     */
    protected $badge;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUid();
    }

    /**
     * {@inheritdoc}
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * {@inheritdoc}
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * {@inheritdoc}
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * {@inheritdoc}
     */
    public function addDevice(DeviceInterface $device)
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeDevice(DeviceInterface $device)
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * {@inheritdoc}
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(TagInterface $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTag(TagInterface $tag)
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date.
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date.
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceByIdentifier($deviceIdentifier)
    {
        $p = function (DeviceInterface $element) use ($deviceIdentifier) {
            return $element->getDeviceIdentifier() == $deviceIdentifier;
        };

        $devices = $this->devices->filter($p);
        if ($devices) {
            return $devices[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceByToken($token)
    {
        $p = function (DeviceInterface $element) use ($token) {
            return $element->getToken() == $token;
        };

        $devices = $this->devices->filter($p);
        if ($devices) {
            return $devices[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUidTag()
    {
        return TagInterface::UID_TAG_PREFIX.$this->getUid();
    }

    /**
     * {@inheritdoc}
     */
    public function getTagNames()
    {
        return $this->tags->map(function (TagInterface $t) {
            return $t->getName();
        });
    }
}
