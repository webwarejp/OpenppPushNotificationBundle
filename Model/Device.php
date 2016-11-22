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
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var \Openpp\MapBundle\Model\PointInterface
     */
    protected $location;

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
     * @var string
     */
    protected $lastDeliveredNotificationId;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var array
     */
    protected static $typeChoices = array(
        self::TYPE_NAME_ANDROID => self::TYPE_ANDROID,
        self::TYPE_NAME_IOS     => self::TYPE_IOS,
        self::TYPE_NAME_WEB     => self::TYPE_WEB,
    );

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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthtoken($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocation(\Openpp\MapBundle\Model\PointInterface $location = null)
    {
        $this->location = $location;
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
     * {@inheritdoc}
     */
    public function setLastDeliveredNotificationId($lastDeliveredNotificationId)
    {
        $this->lastDeliveredNotificationId = $lastDeliveredNotificationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastDeliveredNotificationId()
    {
        return $this->lastDeliveredNotificationId;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return $this->userAgent;
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

    /**
     * {@inheritdoc}
     */
    public static function getTypeChoices()
    {
        return self::$typeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName($type)
    {
        $choices = array_flip(self::$typeChoices);
        if (isset($choices[$type])) {
            return $choices[$type];
        }

        return '';
    }

    /**
     * Gets the parameter. (compatible with Sly\NotificationPusher\Model\Device
     * 
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($key, $default = null)
    {
        if (in_array($key, array('publicKey', 'authToken'))) {
            if (empty($this->$key)) {
                return $default;
            }
            return $this->$key;
        }

        return $default;
    }
}