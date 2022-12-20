<?php

namespace Learner\Doctrine\Event;

use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber as DoctrineSubscriberInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Subscriber implements
        DoctrineSubscriberInterface,
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Learner options
     *
     * @var \Learner\Service\Options
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct (AbstractOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents ()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postLoad
        ];
    }

    /**
     * Events::postPersist event subscriber/listener
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist (LifecycleEventArgs $args)
    {
        $learner = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        // only proceed if the entity is Learner
        if (!($learner instanceof Entity\Learner)) {
            return;
        }

        // set some URLs
        $this->createResetPasswordUrl($learner);
        $this->createLoginUrl($learner);
    }

    /**
     * Events::postUpdate event subscriber/listener
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate (LifecycleEventArgs $args)
    {
        $learner = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        // only proceed if the entity is Learner
        if (!($learner instanceof Entity\Learner)) {
            return;
        }

        // set some URLs
        $this->createResetPasswordUrl($learner);
        $this->createLoginUrl($learner);
    }

    /**
     * PostLoad event subscriber
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad (LifecycleEventArgs $args)
    {
        $learner = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $serviceManager = $this->getServiceLocator();

        // only proceed if the entity is Learner
        if (!($learner instanceof Entity\Learner)) {
            return;
        }

        /* @var $options \Learner\Service\Options */
        $options = $serviceManager->get('Learner\Options');
        $learner->setOptions($options);

        // hydrate settings data to the learner entity
        if (isset($learner['settings'])) {
            $settings = $learner['settings'];
            if ($settings instanceof Collection && !$settings->isEmpty()) {
                foreach ($settings as $setting) {
                    $learner[$setting['name']] = $setting['value'];
                }
            }
        }
    }

    /**
     * Create learner password token and expiry date
     *
     * @param Entity\Learner $learner
     */
    protected function createResetPasswordToken (Entity\Learner $learner)
    {
        $options = $this->options;

        // password token
        $learner['password_token'] = Stdlib\SecurityUtils::generateToken(24);

        // pasword token expiry length
        $expiryLength = $options->getPasswordTokenExpiry();
        $learner['password_token_expiry'] = new \DateTime(date('Y-m-d H:i:s', time() + $expiryLength));
    }

    /**
     * Create the reset password url
     *
     * @param Entity\Learner $learner
     */
    protected function createResetPasswordUrl (Entity\Learner $learner)
    {
        if (!$learner['site']) {
            return;
        }
        if (!(isset($learner['password_token']) && $learner['password_token'])) {
            return;
        }
        $site = $learner['site'];
        $siteUrl = $site['url'];

        $serviceLocator = $this->getServiceLocator();
        if (!($serviceLocator && $serviceLocator->get('Config'))) {
            return;
        }
        $config = $serviceLocator->get('Config')['router'];
        $routeParams = [
            'password_token' => $learner['password_token']
        ];
        $routeOptions = [
            'name' => 'learner/reset-password'
        ];

        $learner['reset_password_url'] = Stdlib\HttpUtils::buildFullUrl($siteUrl, $routeParams, $routeOptions, 'http', $config);
    }

    /**
     * Create the learner's login URL
     *
     * @param Entity\Learner $learner
     */
    protected function createLoginUrl (Entity\Learner $learner)
    {
        if (!$learner['site']) {
            return;
        }
        $site = $learner['site'];
        $siteUrl = $site['url'];
        $identity = $learner['email'] ?  : ($learner['mobile_number'] ?  : ($learner['employment'] && $learner['employment']['employment_id']) ? $learner['employment']['employment_id'] : null);

        $serviceLocator = $this->getServiceLocator();
        if (!($serviceLocator && $serviceLocator->get('Config'))) {
            return;
        }
        $config = $serviceLocator->get('Config')['router'];
        $routeParams = [
            'identity' => $identity
        ];
        $routeOptions = [
            'name' => 'login'
        ];

        $learner['login_url'] = Stdlib\HttpUtils::buildFullUrl($siteUrl, $routeParams, $routeOptions, 'http', $config);
    }
}