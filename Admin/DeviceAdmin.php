<?php

namespace Openpp\PushNotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DeviceAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application')
            ->add('type', null, array('field_type' => 'openpp_push_notification_type_device'))
            ->add('deviceIdentifier')
            ->add('token')
            ->add('registeredAt')
            ->add('unregisteredAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('application')
            ->add('type', 'openpp_push_notification_type_device')
            ->add('user')
            ->add('deviceIdentifier')
            ->add('active', 'boolean')
            ->add('token')
            ->add('registeredAt')
            ->add('unregisteredAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('application', 'sonata_type_model_list')
            ->add('user', 'sonata_type_model_list')
            ->add('deviceIdentifier')
            ->add('type', 'openpp_push_notification_type_device')
            ->add('token')
            ->add('registeredAt', 'sonata_type_datetime_picker', array('dp_use_current' => true))
            ->add('unregisteredAt', 'sonata_type_datetime_picker', array('required' => false))
        ;
    }

    /**
     * @param ShowMapper $showMapper
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
            ->add('registeredAt')
            ->add('unregisteredAt')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
