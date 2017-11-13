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
                        ['openpp.push_notification.push', [
                              'application' => 'viewer',
                              'tagExpression' => 'ios && female',
                              'message' => 'TEST',
                              'options' => [],
                              'operation' => PushServiceManagerInterface::OPERATION_PUSH,
                ]]);

        $this->manager->push('viewer', 'ios && female', 'TEST');
    }

    public function testPushExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('push')
                     ->withConsecutive(['viewer', 'ios && female', 'TEST', []]);

        $this->manager->pushExecute('viewer', 'ios && female', 'TEST');
    }

    public function publishParamProvider()
    {
        return [
            ['viewer', '1234', ['friend_4321', 'friend_2345']],
            ['viewer', '1234', ['friend_4321', 'friend_2345', 'broadcast']],
            ['viewer', '1234', ['friend_4321', 'friend_2345', 'uid_1234']],
        ];
    }

    /**
     * @dataProvider publishParamProvider
     */
    public function testAddTagToUserWithPublish($applicationName, $uid, $tag)
    {
        $valueMap = [
            ['friend_4321', false],
            ['friend_2345', false],
            ['broadcast', true],
            ['uid_1234', true],
        ];
        $this->tagManager->expects($this->any())
                         ->method('isReservedTag')
                         ->will($this->returnValueMap($valueMap));
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                              ['openpp.push_notification.push', [
                                    'application' => $applicationName,
                                    'uid' => $uid,
                                    'tag' => ['friend_4321', 'friend_2345'],
                                    'operation' => PushServiceManagerInterface::OPERATION_ADDTAGTOUSER,
                               ]]);
        $this->manager->addTagToUser($applicationName, $uid, $tag);
    }

    public function noPublishParamProvider()
    {
        return [
            ['viewer', '1234', ['uid_1234', 'broadcast']],
            ['viewer', '1234', 'uid_1234'],
        ];
    }

    /**
     * @dataProvider noPublishParamProvider
     */
    public function testAddTagToUserWithNoPublish($applicationName, $uid, $tag)
    {
        $valueMap = [
            ['friend_4321', false],
            ['friend_2345', false],
            ['broadcast', true],
            ['uid_1234', true],
        ];
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
                     ->withConsecutive(['viewer', '1234', ['friend_4321', 'friend_2345']]);

        $this->manager->addTagToUserExecute('viewer', '1234', ['friend_4321', 'friend_2345']);
    }

    /**
     * @dataProvider publishParamProvider
     */
    public function testRemoveTagFromUser($applicationName, $uid, $tag)
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          ['openpp.push_notification.push', [
                                'application' => $applicationName,
                                'uid' => $uid,
                                'tag' => $tag,
                                'operation' => PushServiceManagerInterface::OPERATION_REMOVETAGFROMUSER,
                          ]]);
        $this->manager->removeTagFromUser($applicationName, $uid, $tag);
    }

    public function testRemoveTagFromUserExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('removeTagFromUser')
                     ->withConsecutive(['viewer', '1234', ['friend_4321', 'friend_2345']]);

        $this->manager->removeTagFromUserExecute('viewer', '1234', ['friend_4321', 'friend_2345']);
    }

    public function testCreateRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          ['openpp.push_notification.push', [
                                'application' => 'viewer',
                                'deviceIdentifier' => 'ABCDEF',
                                'tags' => ['uid_1234', 'android', 'male'],
                                'operation' => PushServiceManagerInterface::OPERATION_CREATE_REGISTRATION,
                ]]);
        $this->manager->createRegistration('viewer', 'ABCDEF', ['uid_1234', 'android', 'male']);
    }

    public function testCreateRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('createRegistration')
                     ->withConsecutive(['viewer', 'ABCDEF', ['uid_1234', 'android', 'male']]);

        $this->manager->createRegistrationExecute('viewer', 'ABCDEF', ['uid_1234', 'android', 'male']);
    }

    public function testUpdateRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          ['openpp.push_notification.push', [
                          'application' => 'viewer',
                          'deviceIdentifier' => 'ABCDEF',
                          'tags' => ['uid_1234', 'android', 'male'],
                          'operation' => PushServiceManagerInterface::OPERATION_UPDATE_REGISTRATION,
                       ]]);
        $this->manager->updateRegistration('viewer', 'ABCDEF', ['uid_1234', 'android', 'male']);
    }

    public function testUpdateRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('updateRegistration')
                     ->withConsecutive(['viewer', 'ABCDEF', ['uid_1234', 'android', 'male']]);

        $this->manager->updateRegistrationExecute('viewer', 'ABCDEF', ['uid_1234', 'android', 'male']);
    }

    public function testDeleteRegistration()
    {
        $this->backend->expects($this->once())
                      ->method('createAndPublish')
                      ->withConsecutive(
                          ['openpp.push_notification.push', [
                          'application' => 'viewer',
                          'type' => 0,
                          'registrationId' => '1234567890',
                          'eTag' => '4',
                          'operation' => PushServiceManagerInterface::OPERATION_DELETE_REGISTRATION,
                      ]]);
        $this->manager->deleteRegistration('viewer', 0, '1234567890', '4');
    }

    public function testDeleteRegistrationExecute()
    {
        $this->pusher->expects($this->once())
                     ->method('deleteRegistration')
                     ->withConsecutive(['viewer', 0, '1234567890', '4']);

        $this->manager->deleteRegistrationExecute('viewer', 0, '1234567890', '4');
    }
}
