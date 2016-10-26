<?php

namespace Openpp\PushNotificationBundle\Model;

class History implements HistoryInterface
{
    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $notificationId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $iconUrl;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var \DateTime
     */
    protected $pushedAt;

    /**
     * @var integer
     */
    protected $sentCount = 0;

    /**
     * @var integer
     */
    protected $deliveredCount = 0;

    /**
     * @var integer
     */
    protected $clickCount = 0;

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
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;

        return $this;
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
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setPushedAt($pushedAt)
    {
        $this->pushedAt = $pushedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPushedAt()
    {
        return $this->pushedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setSentCount($sentCount)
    {
        $this->sentCount = $sentCount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSentCount()
    {
        return $this->sentCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveredCount($deliveredCount)
    {
        $this->deliveredCount = $deliveredCount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveredCount()
    {
        return $this->deliveredCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setClickCount($clickCount)
    {
        $this->clickCount = $clickCount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClickCount()
    {
        return $this->clickCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}