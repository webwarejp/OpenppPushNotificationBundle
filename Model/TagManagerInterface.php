<?php

namespace Openpp\PushNotificationBundle\Model;

interface TagManagerInterface
{
    /**
     * Returns the tag's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Finds a tag by its name.
     *
     * @param string $name
     *
     * @return TagInterface|null
     */
    public function findTagByName($name);

    /**
     * Finds one tag by the given criteria.
     *
     * @param array $criteria
     *
     * @return TagInterface|null
     */
    public function findTagBy(array $criteria);

    /**
     * Returns an empty tag instance
     *
     * @return TagInterface
     */
    public function create();

    /**
     * Reterns whether the tag is reserved.
     *
     * @param string $tag
     *
     * @return boolean
     */
    public function isReservedTag($tag);

    /**
     * Returns the reserved tags.
     *
     * @return array
     */
    public function getReservedTags();

    /**
     * Converts the array of tag names to the array of tag objects.
     *
     * @param string|array  $tags
     * @param boolean $creation create if it does not exist
     *
     * @return array array of TagInterface
     */
    public function getTagObjects($tags, $creation = true);

    /**
     * Converts a tag name to the tag object.
     *
     * @param string  $tag
     * @param boolean $creation create if it does not exist
     * 
     * @return TagInterface|null
     */
    public function getTagObject($tag, $creation);
}
