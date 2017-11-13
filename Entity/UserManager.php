<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Openpp\PushNotificationBundle\Model\UserManager as BaseManager;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Doctrine\ORM\Query\Expr;

class UserManager extends BaseManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Initializes a new UserManager.
     *
     * @param ManagerRegistry $managerRegistry
     * @param string          $class
     */
    public function __construct(ManagerRegistry $managerRegistry, $class)
    {
        $this->objectManager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->objectManager->getRepository($class);

        $metadata = $this->objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user, $andFlush = true)
    {
        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Returns a pager for users.
     *
     * @param array $criteria
     * @param int   $page
     * @param int   $limit
     * @param array $sort
     *
     * @return Pager
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->repository
            ->createQueryBuilder('u')
            ->select('u');

        $fields = $this->objectManager->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }

        if (0 == count($sort)) {
            $sort = ['uid' => 'ASC'];
        }

        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('u.%s', $field), strtoupper($direction));
        }

        if (isset($criteria['application'])) {
            $query->andWhere('u.application = :application');
            $query->setParameter('application', $criteria['application']);
        }

        $query->innerJoin('u.devices', 'd', Expr\Join::WITH, $query->expr()->isNull('d.unregisteredAt'));

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser(ApplicationInterface $application, $uid, $tags, $andFlush = true)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            $user = $this->create();
            $user->setApplication($application);
            $user->setUid($uid);
        }

        if (!is_array($tags)) {
            $tags = [$tags];
        }

        foreach ($tags as $tag) {
            $user->addTag($tag);
        }

        if ($andFlush) {
            $this->objectManager->persist($user);
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUser(ApplicationInterface $application, $uid, $tags, $andFlush = true)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            return;
        }

        if (!is_array($tags)) {
            $tags = [$tags];
        }

        foreach ($tags as $tag) {
            $user->removeTag($tag);
        }

        if ($andFlush) {
            $this->objectManager->persist($user);
            $this->objectManager->flush();
        }
    }
}
