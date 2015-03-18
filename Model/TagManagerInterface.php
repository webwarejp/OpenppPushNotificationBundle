<?php

namespace Openpp\PushNotificationBundle\Model;

interface TagManagerInterface
{
    const BROADCAST_TAG = 'boradcast';

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
     * @return TagInterface or null
    */
    public function findTagByName($name);

    /**
     * Finds one tag by the given criteria.
     *
     * @param array $criteria
    
     * @return TagInterface or null
    */
    public function findTagBy(array $criteria);

    /**
     * Returns an empty tag instance
     *
     * @return TagInterface
    */
    public function createTag();

    /**
     * Check tag expressions.
     * Tag expressions are limited to 20 tags if they contain only ORs; otherwise they are limited to 6 tags.
     *
     * @param string $expression
     *
     * @throws InvalidTagExpressionException
     */
    public function checkTagExpression($expression);

    /**
     * Check the tag(s).
     * A tag can be any string, up to 120 characters, containing alphanumeric and
     * the following non-alphanumeric characters: ‘_’, ‘@’, ‘#’, ‘.’, ‘:’, ‘-’.
     *
     * @param string|array $tag
     *
     * @throws InvalidTagExpressionException
     */
    public function checkTag($tag);

    /**
     * Reterns whether the tag is reserved.
     *
     * @param string $tag
     *
     * @return boolean
     */
    public function isReservedTag($tag);

    /**
     * Converts tag strings to the tag objects.
     *
     * @param string $tags
     * @param boolean $create create if it does not exist
     */
    public function getTagObjects($tags, $create);

    /**
     * Converts a tag string to the tag object.
     *
     * @param string $tag
     * @param boolean $create create if it does not exist
     */
    public function getTagObject($tag, $create);
}