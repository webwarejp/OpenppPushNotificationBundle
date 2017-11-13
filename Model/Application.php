<?php

namespace Openpp\PushNotificationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;

class Application implements ApplicationInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var \Sonata\MediaBundle\Model\MediaInterface
     */
    protected $icon;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $connectionString;

    /**
     * @var string
     */
    protected $hubName;

    /**
     * @var string
     */
    protected $apnsTemplate;

    /**
     * @var string
     */
    protected $gcmTemplate;

    /**
     * @var string
     */
    protected $apnsCertificate;

    /**
     * @var string
     */
    protected $gcmApiKey;

    /**
     * @var ArrayCollection
     */
    protected $users;

    /**
     * @var ArrayCollection
     */
    protected $devices;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @Assert\File()
     */
    protected $apnsCertificateFile;

    /**
     * Initializes a new Application.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon(\Sonata\MediaBundle\Model\MediaInterface $icon = null)
    {
        $this->icon = $icon;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionString()
    {
        return $this->connectionString;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectionString($connectionString)
    {
        $this->connectionString = $connectionString;
    }

    /**
     * {@inheritdoc}
     */
    public function getHubName()
    {
        return $this->hubName;
    }

    /**
     * {@inheritdoc}
     */
    public function setHubName($hubName)
    {
        $this->hubName = $hubName;
    }

    /**
     * {@inheritdoc}
     */
    public function getApnsTemplate()
    {
        return $this->apnsTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function setApnsTemplate($apnsTemplate)
    {
        $this->apnsTemplate = $apnsTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function getGcmTemplate()
    {
        return $this->gcmTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function setGcmTemplate($gcmTemplate)
    {
        $this->gcmTemplate = $gcmTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function getApnsCertificate()
    {
        return $this->apnsCertificate;
    }

    /**
     * {@inheritdoc}
     */
    public function setApnsCertificate($apnsCertificate)
    {
        $this->apnsCertificate = $apnsCertificate;
    }

    /**
     * {@inheritdoc}
     */
    public function getGcmApiKey()
    {
        return $this->gcmApiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setGcmApiKey($gcmApiKey)
    {
        $this->gcmApiKey = $gcmApiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * {@inheritdoc}
     */
    public function addUser(UserInterface $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUser(UserInterface $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * {@inheritdoc}
     */
    public function addDevice(DeviceInterface $device)
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeDevice(DeviceInterface $device)
    {
        $this->devices->removeElement($device);
    }

    /**
     * {@inheritdoc}
     */
    public function countActiveDevices()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('unregisteredAt'));

        return $this->devices->matching($criteria)->count();
    }

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date.
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date.
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getApnsCertificateFile()
    {
        return $this->apnsCertificateFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setApnsCertificateFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null)
    {
        $this->apnsCertificateFile = $file;
    }
}
