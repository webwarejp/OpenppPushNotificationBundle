<?php

namespace Openpp\PushNotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

class DeviceTypeChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getDeviceTypes()
        ));
    }

    /**
     * Returns the device types.
     *
     * @return array
     */
    protected function getDeviceTypes()
    {
        return array(
            DeviceInterface::TYPE_ANDROID => 'Android',
            DeviceInterface::TYPE_IOS     => 'iOS',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'openpp_push_notification_type_device';
    }
}