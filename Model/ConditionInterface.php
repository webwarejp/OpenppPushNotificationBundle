<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\MapBundle\Model\CircleInterface;

interface ConditionInterface
{
    const INTERVAL_TYPE_HOURLY = 1;
    const INTERVAL_TYPE_DAILY = 2;
    const INTERVAL_TYPE_WEEKLY = 3;
    const INTERVAL_TYPE_MONTHLY = 4;

    const TIME_TYPE_SPECIFIC = 1;
    const TIME_TYPE_PERIODIC = 2;
    const TIME_TYPE_CONTINUING = 3;

    /**
     * Returns this condition's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets this condition's name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns whether this condition is enabled.
     *
     * @return bool
     */
    public function isEnable();

    /**
     * Sets whether this condition is enabled.
     *
     * @param bool $enable
     */
    public function setEnable($enable);

    /**
     * Returns the application.
     *
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * Sets the application.
     *
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Returns the notification title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the notification title.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Returns the notification message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Sets the notification message.
     *
     * @param string $message
     */
    public function setMessage($message);

    /**
     * Returns the url of transition on click of the notification.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Sets the url of transition on click of the notification.
     *
     * @param string $url
     */
    public function setUrl($url);

    /**
     * Returns the tag expression.
     *
     * @return string
     */
    public function getTagExpression();

    /**
     * Sets the tag expression.
     *
     * @param string $tagExpression
     */
    public function setTagExpression($tagExpression);

    /**
     * Returns the time type.
     *
     * @return int
     */
    public function getTimeType();

    /**
     * Sets the time type.
     *
     * @param int $timeType
     */
    public function setTimeType($timeType);

    /**
     * Returns the start date.
     *
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * Sets the start datetime.
     *
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate = null);

    /**
     * Returns the end datetime.
     *
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * Sets the end datetime.
     *
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate = null);

    /**
     * Returns the interval type.
     *
     * @return int
     */
    public function getIntervalType();

    /**
     * Sets the interval type.
     *
     * @param int
     */
    public function setIntervalType($intervalType);

    /**
     * Returns the interval.
     *
     * @return int
     */
    public function getIntervalTime();

    /**
     * Sets the interval.
     *
     * @param int
     */
    public function setIntervalTime($intervalTime);

    /**
     * Returns the specific dates.
     *
     * @return \DateTime[]
     */
    public function getSpecificDates();

    /**
     * Sets the specific dates.
     *
     * @param array $specificDates
     */
    public function setSpecificDates(array $specificDates);

    /**
     * Returns the periodic interval.
     *
     * @return \DateInterval
     */
    public function getDateInterval();

    /**
     * Returns the icon for the notification of Web Push.
     *
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getIcon();

    /**
     * Sets the icon for the notification of Web Push.
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $icon
     */
    public function setIcon(\Sonata\MediaBundle\Model\MediaInterface $icon = null);

    /**
     * Returns the circle area.
     *
     * @return CircleInterface
     */
    public function getAreaCircle();

    /**
     * Sets the circle area.
     *
     * @param CircleInterface $areaCircle
     */
    public function setAreaCircle(CircleInterface $areaCircle = null);
}
