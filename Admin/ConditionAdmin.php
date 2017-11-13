<?php

namespace Openpp\PushNotificationBundle\Admin;

use Openpp\PushNotificationBundle\Model\Condition;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ConditionAdmin extends Admin
{
    /**
     * @var bool
     */
    protected $mapBundleEnable = false;

    /**
     * Sets whether the OpenppMapBundle is enabled or not.
     *
     * @param bool $mapBundleEnable
     */
    public function setMapBundleEnable($mapBundleEnable)
    {
        $this->mapBundleEnable = $mapBundleEnable;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application')
            ->add('name')
            ->add('enable')
            ->add('message')
            ->add('tagExpression')
            ->add('timeType', 'doctrine_orm_choice', [], 'choice', [
                'choices' => Condition::getTimeTypeChoices($this->mapBundleEnable),
                'choices_as_values' => true,
                'choice_translation_domain' => 'OpenppPushNotificationBundle',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('application')
            ->add('enable', null, ['editable' => true])
            ->add('message', 'html', ['truncate' => ['length' => 10]])
            ->add('tagExpression', 'html', ['truncate' => ['length' => 10]])
            ->add('timeType', 'choice', [
                'choices' => array_flip(Condition::getTimeTypeChoices($this->mapBundleEnable)),
                'choice_translation_domain' => 'OpenppPushNotificationBundle',
            ])
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
            ->with('form.group_general', ['class' => 'col-md-6'])
                ->add('name')
                ->add('application', 'sonata_type_model_list')
                ->add('message')
                ->add('url', 'url', ['required' => false])
                ->add('enable')
                ->add('tagExpression')
            ->end()
            ->with('form.group_time', ['class' => 'col-md-6'])
                ->add('timeType', 'sonata_type_choice_field_mask', [
                    'required' => true,
                    'choices' => Condition::getTimeTypeChoices($this->mapBundleEnable),
                    'choices_as_values' => true,
                    'choice_translation_domain' => 'OpenppPushNotificationBundle',
                    'map' => [
                        Condition::TIME_TYPE_SPECIFIC => ['specificDates'],
                        Condition::TIME_TYPE_PERIODIC => ['startDate', 'endDate', 'IntervalType'],
                        Condition::TIME_TYPE_CONTINUING => ['startDate', 'endDate'],
                    ],
                ])
                ->add('specificDates', 'sonata_type_native_collection', [
                    'type' => 'sonata_type_datetime_picker',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'options' => [
                        'format' => 'yyyy/MM/dd H:mm',
                        'dp_min_date' => 'new Date()',
                    ],
                ])
                ->add('startDate', 'sonata_type_datetime_picker', [
                    'required' => false,
                    'format' => 'yyyy/MM/dd H:mm',
                    'dp_min_date' => 'new Date()',
                ])
                ->add('endDate', 'sonata_type_datetime_picker', [
                    'required' => false,
                    'format' => 'yyyy/MM/dd H:mm',
                    'dp_min_date' => 'new Date()',
                ])
                ->add('IntervalType', 'choice', [
                    'expanded' => true,
                    'required' => false,
                    'choices' => Condition::getIntervalTypeChoices(),
                    'choices_as_values' => true,
                    'choice_translation_domain' => 'OpenppPushNotificationBundle',
                    'translation_domain' => 'OpenppPushNotificationBundle',
                    'placeholder' => false,
                    'label' => false,
                ])
            ->end()
        ;

        if ($this->mapBundleEnable) {
            $formMapper
                ->with('form.group_location', ['class' => 'col-md-12'])
                    ->add('areaCircle', 'openpp_type_map_geometry_circle', [
                        'required' => false,
                        'label' => false,
                ])
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
    }
}
