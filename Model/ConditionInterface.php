<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * ConditionInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface ConditionInterface
{
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
     * @return boolean
     */
    public function isEnable();

    /**
     * Sets whether this condition is enabled.
     *
     * @param boolean $enable
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
     * @return integer
     */
    public function getIntervalType();

    /**
     * Sets the interval type.
     *
     * @param integer
    */
    public function setIntervalType($intervalType);

    /**
     * Returns the interval.
     *
     * @return integer
     */
    public function getIntervalTime();

    /**
     * Sets the interval.
     *
     * @param integer
     */
    public function setIntervalTime($intervalTime);

    /**
     * Returns the specific dates.
     *
     * @return \Datetime[]
     */
    public function getSpecificDates();

    /**
     * Sets the specific dates.
     *
     * @param array $specificDates
     */
    public function setSpecificDates(array $specificDates);

    /**
     * Returns the area.
     *
     * @return \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface
     */
    public function getArea();

    /**
     * Sets the area.
     *
     * @param \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface $area
    */
    public function setArea(\CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface $area);
}
