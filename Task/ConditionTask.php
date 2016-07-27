<?php

namespace Openpp\PushNotificationBundle\Task;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Collections\DeviceCollection;

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
                if ($devices) {
                    $devices = new DeviceCollection($devices);
                    $this->pushServiceManager->pushToDevices(
                         $condition->getApplication()->getName(),
                         $devices->toIdArray()->toArray(),
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