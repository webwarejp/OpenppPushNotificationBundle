<?php

namespace Openpp\PushNotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Openpp\PushNotificationBundle\Model\Condition;
use Sonata\AdminBundle\Route\RouteCollection;

class ConditionAdmin extends Admin
{
    protected $mapBundleEnable = false;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application')
            ->add('name')
            ->add('enable')
            ->add('message')
            ->add('tagExpression')
            ->add('timeType', 'doctrine_orm_choice', array(), 'choice', array('choices' => Condition::getTimeTypeChoices()))
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
            ->add('message', 'html', array('truncate' => array('length' => 10)))
            ->add('tagExpression', 'html', array('truncate' => array('length' => 10)))
            ->add('timeType', 'choice', array('choices' => Condition::getTimeTypeChoices()))
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
            ->with('form.group_general', array('class' => 'col-md-6'))
                ->add('name')
                ->add('application', 'sonata_type_model_list')
                ->add('message')
                ->add('enable')
                ->add('tagExpression')
            ->end()
            ->with('form.group_time', array('class' => 'col-md-6'))
                ->add('timeType', 'sonata_type_choice_field_mask', array(
                    'required' => true,
                    'choices' => Condition::getTimeTypeChoices($this->mapBundleEnable),
                    'map' => array(
                        Condition::TIME_TYPE_SPECIFIC => array('specificDates'),
                        Condition::TIME_TYPE_PERIODIC => array('startDate', 'endDate', 'IntervalType'),
                        Condition::TIME_TYPE_CONTINUING => array('startDate', 'endDate'),
                    ),
                ))
                ->add('specificDates', 'sonata_type_native_collection', array(
                    'type' => 'sonata_type_datetime_picker',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'options' => array(
                        'dp_use_seconds' => false,
                    ),
                ))
                ->add('startDate', 'sonata_type_datetime_picker', array(
                    'required' => false,
                    'dp_use_seconds' => false,
                    'dp_language' => 'ja',
                ))
                ->add('endDate', 'sonata_type_datetime_picker', array(
                    'required' => false,
                    'dp_use_seconds' => false,
                ))
                ->add('IntervalType', 'choice', array(
                    'expanded' => true,
                    'required' => false,
                    'choices' => Condition::getIntervalTypeChoices(),
                    'placeholder' => false,
                    'label' => false,
                ))
            ->end()
        ;

        if ($this->mapBundleEnable) {
            $formMapper
                ->with('form.group_location', array('class' => 'col-md-12'))
                    ->add('areaCircle', 'openpp_type_map_geometry_circle', array(
                        'required' => false,
                        'label' => false
                ))
                ->end()
            ;
        }
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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
    }

    public function setMapBundleEnable($mapBundleEnable)
    {
        $this->mapBundleEnable = $mapBundleEnable;
    }
}
