<?php

namespace Learner\Validator;

use \Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\AbstractValidator;

class EmploymentIdExists extends AbstractValidator
{
    use ServiceLocatorAwareTrait;
    const ERROR_EXISTS = 'exists';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::ERROR_EXISTS => 'Employment ID "%value%" is already in use.'
    ];

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct (ServiceLocatorInterface $serviceLocator, $options = [])
    {
        $this->setServiceLocator($serviceLocator);

        if ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        parent::__construct($options);
    }

    /**
     * Validate
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
     * @return boolean
     */
    public function isValid ($value, $context = null)
    {
        // set the value to be validated
        $this->setValue($value);

        $validatorManager = $this->getServiceLocator();
        $serviceManager = $validatorManager->getServiceLocator();

        // get the employment repository
        /* @var $service \Learner\Service\LearnerService */
        $service = $serviceManager->get('Learner\Service');
        $repository = $service->employmentRepository();

        $userId = array_key_exists('user_id', $context) ? $context['user_id'] : null;
        $employmentId = $value;
        $routeMatch = $this->getServiceLocator()
            ->getServiceLocator()
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');

        // find if another learner already used the same employment ID
        $employment = $service->findDuplicateEmployment($employmentId, $userId, $siteId);
        if ($employment) {
            $this->error(self::ERROR_EXISTS, $value);
            return false;
        }
        return true;
    }
}