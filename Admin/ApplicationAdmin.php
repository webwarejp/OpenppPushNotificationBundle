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

    protected $apnsCertificateDir;

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
     * Sets the APNS certificate directory to save.
     *
     * @param string $apnsCertificateDir
     */
    public function setApnsCertificateDir($apnsCertificateDir)
    {
        $this->apnsCertificateDir = $apnsCertificateDir;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('packageName')
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
            ->add('packageName')
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
            ->add('packageName')
            ->add('icon', 'sonata_type_model_list', array('required' => false))
            ->add('description')
            ->end();

        switch ($this->pusherName) {
            case "openpp.push_notification.pusher.own":
                $formMapper->with($this->trans('form.group_own_label'))
                        ->add('apnsCertificate', null, array('read_only' => true))
                        ->add('apnsCertificateFile', 'file', array('label' => false, 'required' => false))
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
            ->add('packageName')
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

    /**
     * {@inheritDoc}
     */
    public function prePersist($object)
    {
        $this->uploadApnsCertificate($object);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate($object)
    {
        $this->uploadApnsCertificate($object);
    }

    protected function uploadApnsCertificate($object)
    {
        /* @var $object \Openpp\PushNotificationBundle\Model\ApplicationInterface */
        if ($uploaded = $object->getApnsCertificateFile()) {

            if ($object->getApnsCertificate()) {
                unlink($object->getApnsCertificate());
            }

            $file = $uploaded->move($this->apnsCertificateDir, $uploaded->getClientOriginalName());
            $object->setApnsCertificate($file->getPathname());
            $object->setApnsCertificateFile(null);
        }
    }
}
