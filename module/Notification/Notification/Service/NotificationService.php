<?php

namespace Notification\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class NotificationService extends AbstractService
{

    /**
     * Find all notifications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAll ()
    {
        $repository = $this->notificationRepository();

        // create query
        $qb = $repository->createQueryBuilder('notification')
            ->leftJoin('notification.site', 'site')
            ->leftJoin('notification.learnerNotifications', 'learnerNotification')
            ->leftJoin('notification.groupNotifications', 'groupNotification')
            ->leftJoin('notification.siteNotifications', 'siteNotification')
            ->leftJoin('learnerNotification.learner', 'learner')
            ->leftJoin('groupNotification.group', 'groups')
            ->select('notification, learnerNotification, groupNotification, siteNotification')
            ->add('orderBy', 'notification.subject ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL notifications by site ID
     *
     * @param integer $siteId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllBySiteId ($siteId)
    {
        $repository = $this->notificationRepository();

        // create query
        $qb = $repository->createQueryBuilder('notification')
            ->select('notification, learnerNotification, groupNotification')
            ->leftJoin('notification.site', 'site')
            ->leftJoin('notification.learnerNotifications', 'learnerNotification')
            ->leftJoin('notification.groupNotifications', 'groupNotification')
            ->where('site.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->add('orderBy', 'notification.subject ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL notifications by learner ID
     *
     * @param integer $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllByLearnerId ($userId)
    {
        $repository = $this->notificationRepository();

        // create query
        $qb = $repository->createQueryBuilder('notification')
            ->leftJoin('notification.learnerNotifications', 'learnerNotification')
            ->leftJoin('notification.groupNotifications', 'groupNotification')
            ->leftJoin('learnerNotification.learner', 'learner')
            ->leftJoin('groupNotification.group', 'groups')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->select('notification, learnerNotification, groupNotification')
            ->where('learner.userId = :learnerId OR groupLearners.learner = :learnerId')
            ->setParameter('learnerId', $userId)
            ->add('orderBy', 'notification.subject ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL notifications by learner by user ID and filter by active status
     * and activeFrom and activeTo dates
     *
     * @param integer $userId
     * @return ArrayCollection
     */
    public function findAllActiveByLearnerId ($userId)
    {
        $currentDate = new \DateTime();
        $status = 'active';

        $entityManager = $this->getEntityManager();
        $repository = $this->notificationRepository();
        $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $userId);
        $site = $learner['site'];
        $siteId = $site['site_id'];

        // create query
        $qb = $repository->createQueryBuilder('notification')
            ->leftJoin('notification.learnerNotifications', 'learnerNotification')
            ->leftJoin('notification.groupNotifications', 'groupNotification')
            ->leftJoin('notification.siteNotifications', 'siteNotification')
            ->leftJoin('learnerNotification.learner', 'learner')
            ->leftJoin('groupNotification.group', 'groups')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('siteNotification.site', 'site')
            ->select('notification, learnerNotification, groupNotification, siteNotification, site')
            ->where('(learner.userId = :learnerId OR groupLearners.learner = :learnerId OR site.siteId = :siteId)
                    AND (notification.status IN (:status))
                    AND (notification.activeFrom <= :currentDate OR notification.activeFrom IS NULL)
                    AND (:currentDate <= notification.activeTo OR notification.activeTo IS NULL)')
            ->setParameter('learnerId', $userId)
            ->setParameter('status', (array) $status)
            ->setParameter('currentDate', $currentDate)
            ->setParameter('siteId', $siteId)
            ->add('orderBy', 'notification.subject ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find a single notification given their notification ID
     *
     * @param integer $notificationId
     * @return Entity\Notification
     */
    public function findOneByNotificationId ($notificationId)
    {
        $repository = $this->notificationRepository();

        // create query
        $qb = $repository->createQueryBuilder('notification')
            ->select('notification')
            ->where('notification.notificationId = :notificationId')
            ->setParameter('notificationId', $notificationId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Create ONE notification in repository
     *
     * @param array $data
     * @return Entity\Notification
     */
    public function createNotification ($data, $siteId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // extract data as array
            $data = Stdlib\ObjectUtils::extract($data);

            // get the current site entity instance
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // create new notification entity
            $notification = new Entity\Notification();
            $notification = Stdlib\ObjectUtils::hydrate($data, $notification);
            $notification['site'] = $site;

            // save in repository
            $entityManager->persist($notification);
            $entityManager->flush($notification);
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE notification in repository
     *
     * @param array|\Traversable $data
     * @return Entity\Notification $notification
     * @throws \Exception
     */
    public function updateNotification ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            if (!(isset($data['notification_id']) && $data['notification_id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Update notification requires that notification ID is given'));
            }
            $notificationId = $data['notification_id'];
            $entityManager = $this->getEntityManager();

            // get the notification instance
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);
            $notification = Stdlib\ObjectUtils::hydrate($data, $notification);

            // save in repository
            $entityManager->persist($notification);
            $entityManager->flush($notification);
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save site notification
     *
     * @param integer $siteId
     * @param integer $notificationId
     * @throws Exception
     * @return Entity\Notification
     */
    public function saveSiteNotification ($siteId, $notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);

            // if there are no site, then there is nothing to save
            if (!$siteId) {
                return $notification;
            }
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // check if this notification already exists for the site
            $repository = $entityManager->getRepository('Savvecentral\Entity\NotificationSite');
            $qb = $repository->createQueryBuilder('notificationSite')
                ->select('notificationSite')
                ->where('notificationSite.site = :siteId AND notificationSite.notification = :notificationId')
                ->setParameter('siteId', $siteId)
                ->setParameter('notificationId', $notificationId);
            $notificationSite = $repository->fetchOne($qb);
            if (!$notificationSite) {
                $notificationSite = new Entity\NotificationSite();
                $notificationSite['site'] = $site;
                $notificationSite['notification'] = $notification;
            }
            $entityManager->persist($notificationSite);
            $entityManager->flush($notificationSite);
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete site notification
     *
     * @param integer $siteId
     * @param integer $notificationId
     * @throws Exception
     * @return Entity\Notification
     */
    public function deleteSiteNotification ($siteId, $notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // delete site notification
            $params = [];
            $dql = "DELETE FROM Savvecentral\Entity\NotificationSite siteNotification
                    WHERE siteNotification.site = :siteId
                    AND siteNotification.notification = :notificationId";
            $params['notificationId'] = $notificationId;
            $params['siteId'] = $siteId;
            $entityManager->createQuery($dql)
                ->setParameters($params)
                ->execute();
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save learner notification
     *
     * @param integer|string|array $learnerId
     * @param integer $notificationId
     * @return Entity\Notification
     */
    public function saveLearnerNotification ($learnerId, $notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);

            // convert non-array string/numerics to array
            if (is_string($learnerId) || is_numeric($learnerId)) {
                $learnerId = (array) $learnerId;
            }

            // if no learner IDs, then there is nothing to save
            if (!$learnerId) {
                return $notification;
            }

            // delete learners not in the selected learner IDs
            $params = [];
            $dql = "DELETE FROM Savvecentral\Entity\NotificationLearner learnerNotification
                    WHERE learnerNotification.notification = :notificationId
                    AND learnerNotification.learner NOT IN(:learnerIds)";
            $params['notificationId'] = $notificationId;
			$params['learnerIds'] = $learnerId;
            $entityManager->createQuery($dql)
                ->setParameters($params)
                ->execute();

            // add the selected learners
            foreach ($learnerId as $id) {
                $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $id);

                // check if this learner already exists in the repository
                $dql = "SELECT learnerNotification FROM Savvecentral\Entity\NotificationLearner learnerNotification
                        WHERE learnerNotification.notification = :notificationId
                        AND learnerNotification.learner = :learnerId";
                $learnerNotification = $entityManager->createQuery($dql)
                    ->setParameter('notificationId', $notificationId)
                    ->setParameter('learnerId', $id)
                    ->getOneOrNullResult();

                // if not in repository, create a new learner notification
                if (!$learnerNotification) {
	                $learnerNotification = new Entity\NotificationLearner();
	                $learnerNotification['learner'] = $learner;
	                $learnerNotification['notification'] = $notification;
                }
                $entityManager->persist($learnerNotification);
            }
            $entityManager->flush();
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save learner notification
     *
     * @param integer|string|array $groupId
     * @param integer $notificationId
     * @return Entity\Notification
     */
    public function saveGroupNotification ($groupId, $notificationId)
    {
        try {
            // get the notification entity instance
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);

            // convert non-array string/numerics to array
            if (is_string($groupId) || is_numeric($groupId)) {
                $groupId = (array) $groupId;
            }

            // if no group IDs, then there is nothing to save
            if (!$groupId) {
                return $notification;
            }

            // delete groups not in the selected group IDs
            $dql = "DELETE FROM Savvecentral\Entity\NotificationGroup groupNotification
                    WHERE groupNotification.notification = :notificationId
                    AND groupNotification.group NOT IN(:groupIds)";
            $params['notificationId'] = $notificationId;
            $params['groupIds'] = $groupId;
            $entityManager->createQuery($dql)
                ->setParameters($params)
                ->execute();

            // add the selected groups
            foreach ($groupId as $id) {
                $group = $entityManager->getReference('Savvecentral\Entity\Groups', $id);

                // check if this group and notification already exists in the repository
                $dql = "SELECT groupNotification FROM Savvecentral\Entity\NotificationGroup groupNotification
                        WHERE groupNotification.notification = :notificationId
                        AND groupNotification.group = :groupId";
                $groupNotification = $entityManager->createQuery($dql)
                    ->setParameter('notificationId', $notificationId)
                    ->setParameter('groupId', $id)
                    ->getOneOrNullResult();

                // if not found in repository, create a new learner notification
                if (!$groupNotification) {
                    $groupNotification = new Entity\NotificationGroup();
                    $groupNotification['group'] = $group;
                    $groupNotification['notification'] = $notification;
                }
                $entityManager->persist($groupNotification);
            }
            $entityManager->flush();
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Activate a single notification
     *
     * @param integer $notificationId
     */
    public function activateNotification ($notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);

            $currentDate = new \DateTime(date('Y-m-d'));

            // if the active from date is in the future, move it to current date
            if ($notification->activeFrom >= $currentDate) {
                $notification->activeFrom = $currentDate;
            }

            // if the active to date is in the past, then remove so it is valid
            if ($notification->activeTo <= $currentDate) {
                $notification->activeTo = null;
            }

            $notification->status = 'active';

            $entityManager->persist($notification);
            $entityManager->flush($notification);
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Deactivate a single notification
     *
     * @param integer $notificationId
     */
    public function deactivateNotification ($notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);

            $notification->status = 'inactive';

            $entityManager->persist($notification);
            $entityManager->flush($notification);
            $entityManager->clear();

            return $notification;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a single notification
     *
     * @param integer $notificationId
     */
    public function deleteNotification ($notificationId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $notification = $entityManager->getReference('Savvecentral\Entity\Notification', $notificationId);
            $entityManager->remove($notification);
            $entityManager->flush($notification);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the notification doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function notificationRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Notification');
    }
}