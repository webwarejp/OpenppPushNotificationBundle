<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * Represents a Device model
 *
 * @author shiroko@webware.co.jp
 *
 */
class Device implements DeviceInterface
{
    /**
     * @var string
     */
    protected $deviceIdentifier;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $registrationId;

    /**
     * @var string
     */
    protected $eTag;

    /**
     * @var \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected $application;

    /**
     * @var \Openpp\PushNotificationBundle\Model\UserInterface
     */
    protected $user;

    /**
     * @var \DateTime
     */
    protected $registeredAt;

    /**
     * @var \DateTime
     */
    protected $unregisteredAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function getDeviceIdentifier()
    {
        return $this->deviceIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeviceIdentifier($deviceIdentifier)
    {
        $this->deviceIdentifier = $deviceIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token; 
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * {@inheritdoc}
     */
    public function setRegistrationId($registrationId)
    {
        $this->registrationId = $registrationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getETag()
    {
        return $this->eTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setETag($eTag)
    {
        $this->eTag = $eTag;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Returns whether the device is Android.
     *
     * @return boolean
     */
    public function isAndroid()
    {
        return $this->type === self::TYPE_ANDROID;
    }

    /**
     * Returns whether the device is iOS.
     *
     * @return boolean
     */
    public function isIOS()
    {
        return $this->type === self::TYPE_IOS;
    }

    /**
     * Returns whether the device is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->unregisteredAt === null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setRegisteredAt(\DateTime $registeredAt = null)
    {
        $this->registeredAt = $registeredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnregisteredAt()
    {
        return $this->unregisteredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnregisteredAt(\DateTime $unregisteredAt = null)
    {
        $this->unregisteredAt = $unregisteredAt;
    }

    /**
     * Returns the creation date
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the last update date
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }
}