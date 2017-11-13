<?php

namespace Openpp\PushNotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Openpp\PushNotificationBundle\Model\Device;

class DeviceAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application')
            ->add('type', 'doctrine_orm_choice', [], 'choice', [
                'choices' => Device::getTypeChoices(),
                'choices_as_values' => true,
            ])
            ->add('deviceIdentifier')
            ->add('token')
            ->add('registeredAt')
            ->add('unregisteredAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('application')
            ->add('type', 'choice', [
                'choices' => array_flip(Device::getTypeChoices()),
            ])
            ->add('user')
            ->add('deviceIdentifier')
            ->add('active', 'boolean')
            ->add('token')
            ->add('registeredAt')
            ->add('unregisteredAt')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('application', 'sonata_type_model_list')
            ->add('user', 'sonata_type_model_list')
            ->add('deviceIdentifier')
            ->add('type', 'choice', [
                'choices' => Device::getTypeChoices(),
                'choices_as_values' => true,
            ])
            ->add('token')
            ->add('registeredAt', 'sonata_type_datetime_picker', ['dp_use_current' => true])
            ->add('unregisteredAt', 'sonata_type_datetime_picker', ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('application')
            ->add('user')
            ->add('deviceIdentifier')
            ->add('type')
            ->add('token')
            ->add('registrationId')
            ->add('eTag')
            ->add('registeredAt')
            ->add('unregisteredAt')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
