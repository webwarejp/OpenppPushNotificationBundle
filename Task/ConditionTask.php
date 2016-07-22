<?php

namespace Openpp\PushNotificationBundle\Task;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class ConditionTask
{
    protected $conditionManager;
    protected $pushServiceManager;
    protected $deviceManager;

    /**
     * Constructor
     *
     * @param ConditionManagerInterface   $conditionManager
     * @param PushServiceManagerInterface $pushServiceManager
     * @param DeviceManagerInterface      $deviceManager
     */
    public function __construct(ConditionManagerInterface $conditionManager, PushServiceManagerInterface $pushServiceManager, DeviceManagerInterface $deviceManager)
    {
        $this->conditionManager   = $conditionManager;
        $this->pushServiceManager = $pushServiceManager;
        $this->deviceManager      = $deviceManager;
    }

    /**
     * 
     * @param string $time
     * @param string $margin
     */
    public function execute($time = null, $margin = null)
    {
        $conditions = $this->conditionManager->matchConditionByTime(
            $time ? new \DateTime($time) : new \DateTime(),
            $margin ? new \DateInterval('PT'. $margin . 'M') : null
        );

        foreach ($conditions as $condition) {
            if ($condition->getAreaCircle()) {
                $devices = $this->deviceManager->findDevicesInAreaCircleWithTag(
                    $condition->getApplication(),
                    $condition->getTagExpression(),
                    $condition->getAreaCircle()
                );
                foreach ($devices as $device) {
                    $tagExpression = $condition->getTagExpression();
                    $uidTag = $device->getUser()->getUidTag();
                    $tagExpression = $tagExpression ? '(' . $tagExpression . ') && ' . $uidTag : $uidTag;
                    $this->pushServiceManager->push(
                        $condition->getApplication()->getName(),
                        $tagExpression,
                        $condition->getMessage()
                    );
                }
            } else {
                $this->pushServiceManager->push(
                    $condition->getApplication()->getName(),
                    $condition->getTagExpression(),
                    $condition->getMessage()
                );
            }
        }

        return $conditions;
    }
}