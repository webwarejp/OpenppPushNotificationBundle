<?php

namespace Openpp\PushNotificationBundle\Tests\Pusher;

use Openpp\PushNotificationBundle\Pusher\PushServiceManager;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;

class PushServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $tagManager;
    protected $backend;
    protected $pusher;
    protected $manager;

    public function setUp()
    {
        $this->tagManager = $this->getMockBuilder('Openpp\PushNotificationBundle\Model\TagManagerInterface')
                           ->getMock();
        $this->pusher = $this->getMockBuilder('Openpp\PushNotificationBundle\Pusher\PusherInterface')
                            ->getMock();

        $this->backend = $this->getMockBuilder('Sonata\NotificationBundle\Backend\BackendInterface')
                              ->getMock();

        $this->manager = new PushServiceManager($this->backend, $this->tagManager, $this->pusher);
    }

    public function testGetPusher()
    {
        $this->assertSame($this->pusher, $this->manager->getPusher());
    }

    public function testPush()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                        array('openpp.push_notification.push', array(
                              'application'   => 'viewer',
                              'tagExpression' => 'ios && female',
                              'message'       => 'TEST',
                              'options'       => array(),
                              'operation'     => PushServiceManagerInterface::OPERATION_PUSH,
                )));

        $this->manager->push('viewer', 'ios && female', 'TEST');
    }

    public function testPushExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('push')
                     ->withConsecutive(array('viewer', 'ios && female', 'TEST', array()));

        $this->manager->pushExecute('viewer', 'ios && female', 'TEST');
    }

    public function publishParamProvider()
    {
        return array(
            array('viewer', '1234', array('friend_4321', 'friend_2345')),
            array('viewer', '1234', array('friend_4321', 'friend_2345', 'broadcast')),
            array('viewer', '1234', array('friend_4321', 'friend_2345', 'uid_1234'))
        );
    }

    /**
     * 
     * @dataProvider publishParamProvider
     */
    public function testAddTagToUserWithPublish($applicationName, $uid, $tag)
    {
        $valueMap = array(
            array('friend_4321', false),
            array('friend_2345', false),
            array('broadcast', true),
            array('uid_1234', true),
        );
        $this->tagManager->expects($this->any())
                         ->method('isReservedTag')
                         ->will($this->returnValueMap($valueMap));
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                              array('openpp.push_notification.push', array(
                                    'application' => $applicationName,
                                    'uid'         => $uid,
                                    'tag'         => array('friend_4321', 'friend_2345'),
                                    'operation'   => PushServiceManagerInterface::OPERATION_ADDTAGTOUSER,
                               )));
        $this->manager->addTagToUser($applicationName, $uid, $tag);
    }

    public function noPublishParamProvider()
    {
        return array(
            array('viewer', '1234', array('uid_1234', 'broadcast')),
            array('viewer', '1234', 'uid_1234')
        );
    }

    /**
     *
     * @dataProvider noPublishParamProvider
     */
    public function testAddTagToUserWithNoPublish($applicationName, $uid, $tag)
    {
        $valueMap = array(
            array('friend_4321', false),
            array('friend_2345', false),
            array('broadcast', true),
            array('uid_1234', true),
        );
        $this->tagManager->expects($this->any())
                         ->method('isReservedTag')
                         ->will($this->returnValueMap($valueMap));
        $this->backend->expects($this->never())
                      ->method('createAndPublish');

        $this->manager->addTagToUser($applicationName, $uid, $tag);
    }

    public function testAddTagToUserExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('addTagToUser')
                     ->withConsecutive(array('viewer', '1234', array('friend_4321', 'friend_2345')));

        $this->manager->addTagToUserExecute('viewer', '1234', array('friend_4321', 'friend_2345'));
    }

    /**
     *
     * @dataProvider publishParamProvider
     */
    public function testRemoveTagFromUser($applicationName, $uid, $tag)
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          array('openpp.push_notification.push', array(
                                'application' => $applicationName,
                                'uid'         => $uid,
                                'tag'         => $tag,
                                'operation'   => PushServiceManagerInterface::OPERATION_REMOVETAGFROMUSER,
                          )));
        $this->manager->removeTagFromUser($applicationName, $uid, $tag);
    }

    public function testRemoveTagFromUserExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('removeTagFromUser')
                     ->withConsecutive(array('viewer', '1234', array('friend_4321', 'friend_2345')));

        $this->manager->removeTagFromUserExecute('viewer', '1234', array('friend_4321', 'friend_2345'));
    }

    public function testCreateRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          array('openpp.push_notification.push', array(
                                'application'      => 'viewer',
                                'deviceIdentifier' => 'ABCDEF',
                                'tags'             => array('uid_1234', 'android', 'male'),
                                'operation'   => PushServiceManagerInterface::OPERATION_CREATE_REGISTRATION,
                )));
        $this->manager->createRegistration('viewer', 'ABCDEF', array('uid_1234', 'android', 'male'));
    }

    public function testCreateRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('createRegistration')
                     ->withConsecutive(array('viewer', 'ABCDEF', array('uid_1234', 'android', 'male')));

        $this->manager->createRegistrationExecute('viewer', 'ABCDEF', array('uid_1234', 'android', 'male'));
    }

    public function testUpdateRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          array('openpp.push_notification.push', array(
                          'application'      => 'viewer',
                          'deviceIdentifier' => 'ABCDEF',
                          'tags'             => array('uid_1234', 'android', 'male'),
                          'operation'   => PushServiceManagerInterface::OPERATION_UPDATE_REGISTRATION,
                       )));
        $this->manager->updateRegistration('viewer', 'ABCDEF', array('uid_1234', 'android', 'male'));
    }

    public function testUpdateRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('updateRegistration')
                     ->withConsecutive(array('viewer', 'ABCDEF', array('uid_1234', 'android', 'male')));

        $this->manager->updateRegistrationExecute('viewer', 'ABCDEF', array('uid_1234', 'android', 'male'));
    }

    public function testDeleteRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          array('openpp.push_notification.push', array(
                          'application'      => 'viewer',
                          'type'             => 0,
                          'registrationId'   => '1234567890',
                          'eTag'             => '4',
                          'operation'   => PushServiceManagerInterface::OPERATION_DELETE_REGISTRATION,
                      )));
        $this->manager->deleteRegistration('viewer', 0, '1234567890', '4');
    }

    public function testDeleteRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('deleteRegistration')
                     ->withConsecutive(array('viewer', 0, '1234567890', '4'));

        $this->manager->deleteRegistrationExecute('viewer', 0, '1234567890', '4');
    }
}