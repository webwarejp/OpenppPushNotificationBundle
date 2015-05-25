<?php

namespace Openpp\PushNotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ConditionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application')
            ->add('name')
            ->add('message')
            ->add('tagExpression')
            ->add('enable')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('application')
            ->add('enable', null, array('editable' => true))
            ->add('createdAt')
            ->add('updatedAt')
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
            ->add('name')
            ->add('application', 'sonata_type_model_list')
            ->add('message')
            ->add('tagExpression')
            ->add('startDate', 'sonata_type_datetime_picker', array('required' => false))
            ->add('endDate', 'sonata_type_datetime_picker', array('required' => false))
            ->add('interval')
            ->add('specificDates')
            ->add('area', 'openpp_type_map_geometry', array('required' => false))
            ->add('enable')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('application')
            ->add('message')
            ->add('tagExpression')
            ->add('area')
            ->add('startDate')
            ->add('endDate')
            ->add('interval')
            ->add('specificDates')
            ->add('enable')
            ->add('createdAt')
            ->add('updatedAt')

        ;
    }
}
