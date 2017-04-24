<?php

namespace Openpp\PushNotificationBundle\Task;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Collections\DeviceCollection;
use Sonata\MediaBundle\Twig\Extension\MediaExtension;

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
    protected $mediaExtension;

    /**
     * Constructor
     *
     * @param ConditionManagerInterface   $conditionManager
     * @param PushServiceManagerInterface $pushServiceManager
     * @param DeviceManagerInterface      $deviceManager
     */
    public function __construct(ConditionManagerInterface $conditionManager, PushServiceManagerInterface $pushServiceManager, DeviceManagerInterface $deviceManager, MediaExtension $mediaExtension)
    {
        $this->conditionManager   = $conditionManager;
        $this->pushServiceManager = $pushServiceManager;
        $this->deviceManager      = $deviceManager;
        $this->mediaExtension     = $mediaExtension;
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
            $options = array();
            if (!empty($condition->getTitle()))
            {
                $options['title'] = $condition->getTitle();
            }
            if (!empty($condition->getUrl())) {
                $options['url'] = $condition->getUrl();
            }
            if (!empty($condition->getIcon())) {
                $options['icon'] = $this->mediaExtension->path($condition->getIcon(), 'reference');
            }

            if ($condition->getAreaCircle()) {
                $devices = $this->deviceManager->findDevicesInAreaCircleWithTag(
                    $condition->getApplication(),
                    $condition->getTagExpression(),
                    $condition->getAreaCircle()
                );
                if ($devices) {
                    $devices = new DeviceCollection($devices);
                    $this->pushServiceManager->pushToDevices(
                         $condition->getApplication()->getPackageName(),
                         $devices->toIdArray()->toArray(),
                         $condition->getMessage(),
                         $options
                    );
                }
            } else {
                $this->pushServiceManager->push(
                    $condition->getApplication()->getPackageName(),
                    $condition->getTagExpression(),
                    $condition->getMessage(),
                    $options
                );
            }
        }

        return $conditions;
    }
}
