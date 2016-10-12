<?php

namespace Openpp\PushNotificationBundle\Model;

interface HistoryManagerInterface
{
    /**
     * Returns the history's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Returns an empty history instance
     *
     * @return HistoryInterface
     */
    public function create();

    /**
     * Saves a history.
     *
     * @param HistoryInterface $history
     * @param boolean $andFlush
     *
     * @return void
     */
    public function save(HistoryInterface $history, $andFlush = true);

    /**
     * Finds one history by the given criteria.
     *
     * @param array $criteria
     *
     * @return HistoryInterface or null
     */
    public function findHistoryBy(array $criteria);

    /**
     * Finds multiple histories by the given criteria.
     *
     * @param array $criteria
     *
     * @return array
     */
    public function findHistoriesBy(array $criteria);
}
