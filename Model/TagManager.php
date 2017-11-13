<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class TagManager implements TagManagerInterface
{
    /**
     * @var array
     */
    protected $reservedTags = [
        TagInterface::UID_TAG_PREFIX,
        DeviceInterface::TYPE_NAME_ANDROID,
        DeviceInterface::TYPE_NAME_IOS,
        DeviceInterface::TYPE_NAME_WEB,
    ];

    /**
     * @var array
     */
    protected $reservedTagPatterns;

    /**
     * Initializes a new TagManager.
     */
    public function __construct()
    {
        $this->reservedTagPatterns = [];
        foreach ($this->reservedTags as $tag) {
            if (preg_match('/_$/', $tag)) {
                $this->reservedTagPatterns[] = '/^'.$tag.'/';
            } else {
                $this->reservedTagPatterns[] = '/^'.$tag.'$/';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $tag = new $class();

        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function findTagByName($name)
    {
        return $this->findTagBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getReservedTags()
    {
        return $this->reservedTags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagObjects($tags, $creation = true)
    {
        $objects = [];

        if (!is_array($tags)) {
            $tags = [$tags];
        }

        foreach ($tags as $tag) {
            $object = $this->getTagObject($tag, $creation);
            if (!empty($object)) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagObject($tag, $creation = true)
    {
        $object = $this->findTagByName($tag);

        if (empty($object) && $creation) {
            $object = $this->create();
            $object->setName($tag);
        }

        return $object;
    }
}
