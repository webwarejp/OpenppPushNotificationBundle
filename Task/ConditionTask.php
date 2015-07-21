<?php

namespace Openpp\PushNotificationBundle\Task;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class ConditionTask extends ContainerAware
{
    /**
     * 
     * @param string $time
     * @param string $margin
     */
    public function execute($time = null, $margin = null)
    {
        $conditionManager = $this->container->get('openpp.push_notification.manager.condition');
        $conditions = $conditionManager->matchConditionByTime(
                $time ? $time : new \Datetime(),
                $margin ? new \DateInterval('PT'. $margin . 'M') : null
        );

        foreach ($conditions as $condition) {
            $application = $conditon->getApplication()->getName();
            $target = $condition->getTagExpression();
            $message = $condition->getMessage();
            $this->container->get('openpp.push_notification.push_service_manager')->push($application, $target, $message);
        }

        return $conditions;
    }
}