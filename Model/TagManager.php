<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException;

abstract class TagManager implements TagManagerInterface
{
    const MAX_TAG_LENGTH = 120;
    const MAX_TAGS_WITH_ONLY_OR_OPERATORS = 20;
    const MAX_TAGS_WITH_VALIOUS_OPERATORS = 6;

    protected $reservedTags = array(
        self::BROADCAST_TAG,
        self::UID_TAG_PREFIX,
    );

    protected $reservedTagPatterns;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservedTagPatterns = array();
        foreach ($this->reservedTags as $tag) {
            if (preg_match('/_$/', $tag)) {
                $this->reservedTagPatterns[] = '/^' . $tag . '/';
            } else {
                $this->reservedTagPatterns[] = '/^' . $tag . '$/';
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createTag()
    {
        $class = $this->getClass();
        $tag = new $class;

        return $tag;
    }

    /**
     * {@inheritDoc}
     */
    public function findTagByName($name)
    {
        $this->findTagBy(array('name' => $name));
    }

    /**
     * {@inheritDoc}
     */
    public function checkTagExpression($expression)
    {
        $tags = array();
        $strs = preg_split('/(&&|\|\|)/', $expression);

        foreach ($strs as $str) {
            $str = trim($str);
            $str = trim($str, '()!');
            $tags[] = $str;
        }

        $this->checkTag($tags);

        $orOnly = true;
        if (preg_match_all('/(&&|\|\||!)/', $expression, $operators)) {
            if (in_array('&&', $operators[0]) || in_array('!', $operators[0])) {
                $orOnly = false;
            }
        }

        if ($orOnly && self::MAX_TAGS_WITH_ONLY_OR_OPERATORS < count($tags)) {
            throw new InvalidTagExpressionException('Tag expressions are limited to 20 tags if they contain only ORs.');
        } elseif (!$orOnly && self::MAX_TAGS_WITH_VALIOUS_OPERATORS < count($tags)) {
            throw new InvalidTagExpressionException('Tag expressions are limited to 6 tags if they contain except ORs.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function checkTag($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $this->checkSingleTag($tag);
        }
    }

    /**
     * Check a tag.
     *
     * @param string $tag
     *
     * @throws InvalidTagExpressionException
     */
    public function checkSingleTag($tag) {
        if (self::MAX_TAG_LENGTH < strlen($tag)) {
            throw new InvalidTagExpressionException('A tag can be up to 120 characters: ' . $tag);
        }

        if (!preg_match('/^[a-zA-Z0-9_@#\.:\-]+$/', $tag)) {
            throw new InvalidTagExpressionException("A tag can be containing alphanumeric and the following non-alphanumeric characters: ‘_’, ‘@’, ‘#’, ‘.’, ‘:’, ‘-’: " . $tag);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isReservedTag($tag)
    {
        foreach ($this->reservedTagPatterns as $pattern) {
            if (preg_match($pattern, $tag)) {

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getTagObjects($tags, $creation = true)
    {
        $objects = array();

        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $object = $this->getTagObject($tag, $creation);
                if ($object) {
                    $objects[] = $object;
                }
            }
        } else {
            $object = $this->getTagObject($tags, $creation);
            if ($object) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    /**
     * {@inheritDoc}
     */
    public function getTagObject($tag, $creation = true)
    {
        $object = $this->findTagByName($tag);

        if (!$object && $creation) {
            $object = $this->createTag();
            $object->setName($tag);
        }

        return $object;
    }
}
