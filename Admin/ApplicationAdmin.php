<?php

namespace Openpp\PushNotificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ApplicationAdmin extends Admin
{
    protected $pusherName;

    /**
     * Sets the pusher name.
     *
     * @param string $pusherName
     */
    public function setPusherName($pusherName)
    {
        $this->pusherName = $pusherName;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('description')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('description')
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
            ->add('description')
            ->end();

        switch ($this->pusherName) {
            case "openpp.push_notification.pusher.own":
                $formMapper->with($this->trans('form.group_own_label'))
                        ->add('apnsCertificate')
                        ->add('gcmApiKey')
                    ->end()
                ;
                break;

            case "openpp.push_notification.pusher.azure":
                $formMapper->with($this->trans('form.group_azure_label'))
                        ->add('hubName')
                        ->add('connectionString')
                        ->add('apnsTemplate')
                        ->add('gcmTemplate')
                    ->end()
                ;
                break;
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
            ->add('description')
            ->add('apnsCertificate')
            ->add('gcmApiKey')
            ->add('hubName')
            ->add('connectionString')
            ->add('apnsTemplate')
            ->add('gcmTemplate')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
