<?php

namespace Openpp\PushNotificationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a Application model
 *
 * @author shiroko@webware.co.jp
 *
 */
class Application implements ApplicationInterface
{
    /**
     * @var string
     */
    protected $name;

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
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Returns a string representation
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