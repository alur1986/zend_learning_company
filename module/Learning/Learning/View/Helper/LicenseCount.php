<?php

namespace Learning\View\Helper;

use Savve\Stdlib\Exception;
use Savve\View\Helper\AbstractViewHelper;
use Zend\Config\Config;

class LicenseCount extends AbstractViewHelper
{
    /**
     * Current activity id
     *
     * @var integer
     */
    protected $activityId = false;

    /**
     * Constructor
     *
     * @param array|\Zend\Config\Config $sm
     */
    public function __construct ($sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     *
     * @param $activityId
     *
     * @return $this
     */
    public function __invoke ($activityId)
    {
    //    $serviceManager = $this->getServiceManager();
        $serviceManager = $this->serviceManager;
    //    $serviceManager = $serviceLocator->getServiceLocator();

        if (isset($activityId) && is_numeric($activityId)) {
            /* @var $service \Distribution\Learning\Service\LearningDistributionService */
            $service = $serviceManager->get('Distribution\Learning');
            $learners = $service->findAllActiveLearnersByActivityId ($activityId);

            return count($learners);
        } else {
            throw new Exception\InvalidArgumentException(sprintf('Activity ID not found. Please ensure that an Activity ID is passed to the License Count Helper.'));
        }

    }

    /**
     * Method overloading to display the activity title or type when the object is treated as a string
     *
     * @return string
     */
    public function toString ()
    {
        return parent::__toString();
    }
}