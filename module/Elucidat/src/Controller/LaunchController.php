<?php

namespace Elucidat\Controller;

use Doctrine\ORM\EntityManager;
use Savve\Stdlib\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Savvecentral\Entity\ElucidatUser;
use Zend\Mvc\Controller\AbstractActionController;

class LaunchController extends AbstractActionController
{

    /**
     * Doctrine Entity Manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Launch Elucidat
     */
    public function launchAction(){
        $authorId = $this->params('author_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $author = $service->findOneAuthorByAuthorId($authorId);
        $account = $author["account"];


    }
}