<?php

namespace Openpp\PushNotificationBundle\Model;


abstract class TagManager implements TagManagerInterface
{
    protected $reservedTags = array(
        TagInterface::UID_TAG_PREFIX,
        DeviceInterface::TYPE_NAME_ANDROID,
        DeviceInterface::TYPE_NAME_IOS,
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
    public function create()
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
        return $this->findTagBy(array('name' => $name));
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

        if (!is_array($tags)) {
            $tags = array($tags); 
        }

        foreach ($tags as $tag) {
            $object = $this->getTagObject($tag, $creation);
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
            $object = $this->create();
            $object->setName($tag);
        }

        return $object;
    }
}
