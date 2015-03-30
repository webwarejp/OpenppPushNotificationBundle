<?php

namespace Openpp\PushNotificationBundle\Tests\Model;

use Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException;

class TagManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $tagManager;

    public function setUp()
    {
        $this->tagManager = $this->getMockForAbstractClass('Openpp\PushNotificationBundle\Model\TagManager');
    }

    public function reservedTagProvider()
    {
        return array(array('broadcast'), array('uid_123456'));
    }

    /**
     * 
     * @dataProvider reservedTagProvider
     */
    public function testIsReservedTag($tag)
    {
        $result = $this->tagManager->isReservedTag($tag);
        $this->assertTrue($result);
    }

    public function nonReservedTagProvider()
    {
        return array(array('broadcasting'), array('uid123456'), array('friend_1234'));
    }

    /**
     *
     * @dataProvider nonReservedTagProvider
     */
    public function testIsReservedTagWithNonReservedTag($tag)
    {
        $result = $this->tagManager->isReservedTag($tag);
        $this->assertFalse($result);
    }

    public function validTagProvider()
    {
        return array(
            array('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@@@@@@@@@@:::::::::::___________..........#########-------'),
            array('android'),
        );
    }

    /**
     *
     * @dataProvider validTagProvider
     */
    public function testCheckSingleTagWithTheValidTag($tag)
    {
        try {
            $this->tagManager->checkSingleTag($tag);
        } catch (InvalidTagExpressionException $e) {
            $this->fail('Exception occured: ' . $e->getMessage());
        }
    }

    public function invalidTagProvider()
    {
        return array(
            array('0ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@@@@@@@@@@:::::::::::___________..........#########-------'),
            array('android?4'),
            array('tag~2'),
            array('tag%6'),
            array('tag&5'),
            array('タグ'),
            array('tag!!!'),
        );
    }

    /**
     *
     * @dataProvider invalidTagProvider
     * @expectedException Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException
     */
    public function testCheckSingleTagWithTheInvalidTag($tag)
    {
        $this->tagManager->checkSingleTag($tag);
    }

    public function validTagExpressionProvider()
    {
        return array(
            array('uid_1 || uid_2 || uid_3 || uid_4 || uid_5 || uid_6 || uid_7 || uid_8 || uid_9 || uid_10 || uid_11 || uid_12 || uid_13 || uid_14 || uid_15 || uid_16 || uid_17 || uid_18 || uid_19 || uid_20'),
            array('(android && female) || (ios && male) || !japanese || amazon'),
            array('android')
        );
    }

    /**
     * 
     * @dataProvider validTagExpressionProvider
     */
    public function testCheckTagExpressionWithTheValidTagExpression($tagExpression)
    {
        try {
            $this->tagManager->checkTagExpression($tagExpression);
        } catch (InvalidTagExpressionException $e) {
            $this->fail('Exception occured: ' . $e->getMessage());
        }
    }

    public function invalidTagExpressionProvider()
    {
        return array(
            array('uid_1 || uid_2 || uid_3 || uid_4 || uid_5 || uid_6 || uid_7 || uid_8 || uid_9 || uid_10 || uid_11 || uid_12 || uid_13 || uid_14 || uid_15 || uid_16 || uid_17 || uid_18 || uid_19 || uid_20 || uid_21'),
            array('(android && female) || (ios && male) || !japanese || amazon || windows'),
        );
    }

    /**
     *
     * @dataProvider invalidTagExpressionProvider
     * @expectedException Openpp\PushNotificationBundle\Exception\InvalidTagExpressionException
     */
    public function testCheckTagExpressionWithTheInvalidTagExpression($tagExpression)
    {
        $this->tagManager->checkTagExpression($tagExpression);
    }
}
