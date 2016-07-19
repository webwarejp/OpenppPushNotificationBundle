<?php

namespace Openpp\PushNotificationBundle\Task;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class ConditionTask
{
    protected $conditionManager;
    protected $pushServiceManager;
    protected $userManager;

    /**
     * Constructor
     *
     * @param ConditionManagerInterface $conditionManager
     * @param PushServiceManagerInterface $pushServiceManager
     */
    public function __construct(ConditionManagerInterface $conditionManager, PushServiceManagerInterface $pushServiceManager, UserManagerInterface $userManager)
    {
        $this->conditionManager   = $conditionManager;
        $this->pushServiceManager = $pushServiceManager;
        $this->userManager        = $userManager;
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
                $users = $this->userManager->findUserInAreaCircleWithTag(
                    $condition->getApplication(),
                    $condition->getTagExpression(),
                    $condition->getAreaCircle()
                );
                foreach ($users as $user) {
                    $this->pushServiceManager->push(
                        $condition->getApplication()->getName(),
                        $user->getUidTag(),
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