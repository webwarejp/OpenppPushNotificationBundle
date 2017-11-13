<?php

namespace Openpp\PushNotificationBundle\Model;

interface HistoryInterface
{
    /**
     * Set application.
     *
     * @param ApplicationInterface $application
     *
     * @return History
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Get application.
     *
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * Set notificationId.
     *
     * @param string $notificationId
     *
     * @return History
     */
    public function setNotificationId($notificationId);

    /**
     * Get notificationId.
     *
     * @return string
     */
    public function getNotificationId();

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return History
     */
    public function setTitle($title);

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return History
     */
    public function setMessage($message);

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set icon url.
     *
     * @param string $iconUrl
     *
     * @return History
     */
    public function setIconUrl($iconUrl);

    /**
     * Get icon url.
     *
     * @return string
     */
    public function getIconUrl();

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return History
     */
    public function setUrl($url);

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set pushedAt.
     *
     * @param \DateTime $pushedAt
     *
     * @return History
     */
    public function setPushedAt($pushedAt);

    /**
     * Get pushedAt.
     *
     * @return \DateTime
     */
    public function getPushedAt();

    /**
     * Set sent count.
     *
     * @param int $sentCount
     *
     * @return History
     */
    public function setSentCount($sentCount);

    /**
     * Get sent count.
     *
     * @return int
     */
    public function getSentCount();

    /**
     * Set delivered count.
     *
     * @param int $deliveredCount
     *
     * @return History
     */
    public function setDeliveredCount($deliveredCount);

    /**
     * Get delivered count.
     *
     * @return int
     */
    public function getDeliveredCount();

    /**
     * Set click count.
     *
     * @param int $clickCount
     *
     * @return History
     */
    public function setClickCount($clickCount);

    /**
     * Get sent count.
     *
     * @return int
     */
    public function getClickCount();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return History
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return History
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
