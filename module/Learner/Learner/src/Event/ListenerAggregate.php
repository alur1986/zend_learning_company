<?php
/**
 * @deprecated
 */
namespace Learner\Event;

use Learner\Event\Event as LearnerEvent;
use Savve\EventListenerManager\AbstractListenerAggregate;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\View\ViewEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use Zend\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Zend\Db\Sql\Expression;
use Zend\EventManager\EventManagerInterface;

class ListenerAggregate extends AbstractListenerAggregate
{

    /**
     * Attach one or more listeners
     *
     * @return void
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach (EventManagerInterface $event)
    {
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManager */
        $sharedEventManager = $event->getSharedManager();

        /* @formatter:off */

        // learner action event listeners
        $this->listeners[] = $sharedEventManager->attach('Learner\Service', LearnerEvent::EVENT_BULK . '.pre', [ $this, 'preLearnerBulkUpload' ], 100);
        $this->listeners[] = $sharedEventManager->attach('Learner\Service', LearnerEvent::EVENT_ACTIVATE. '.pre', [ $this, 'preLearnerActivate' ], 100);

        /* @formatter:on */
    }

    /**
     * LearnerEvent:EVENT_BULK event listener
     *
     * @param LearnerEvent $event
     */
    public function preLearnerBulkUpload (LearnerEvent $event)
    {
        /* @var $service \Learner\Service\LearnerService */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

        $service = $event->getTarget();
        $serviceManager = $service->getServiceLocator();
        $learners = $event->getParam('learners');
        $numUploadLearners = count($learners);

        // get the company licence for this site
        $licence = $serviceManager->get("Licence\Company\Current");
        $validFrom = $licence['valid_from'];
        $validTo = $licence['valid_to'];
        $status = $licence['status'];
        $numLearners = $licence['num_learners'];

        // get the site details
        $site = $serviceManager->get('Site');

        // if licence status is null/false or inactive/expired/deleted or validFrom and validTo is null/false, do not continue
        if (!$status || in_array($status, [ 'inactive', 'expired', 'deleted' ]) || !($validFrom && $validTo)) {
            return ;
        }

        // get the available number of learners in the licence
        $availableNumLearners = $licence['num_learners'] - $site['num_learners'];

		// if the number of learners to upload/import exceeds the number of available learner records, then do not continue;
		if ($availableNumLearners < $numUploadLearners) {
		    $difference = $numUploadLearners - $availableNumLearners;
		    throw new Exception\DomainException(sprintf('There are only %s available spaces to add learners in the platform, the number of learners you are trying to import is %s and exceeds the licence by %s learners.', $availableNumLearners, $numUploadLearners, $difference), LearnerEvent::ERROR_CODE_MAX_REACHED);
		}
    }

    /**
     * LearnerEvent::EVENT_ACTIVATE event listener
     * @param LearnerEvent $event
     * @throws Exception\DomainException
     * @throws \Exception
     */
    public function preLearnerActivate (LearnerEvent $event)
    {
        /* @var $service \Learner\Service\LearnerService */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

		$service = $event->getTarget();
        $serviceManager = $service->getServiceLocator();
        $learner = $event->getParam('learner');
        $numUploadLearners = 1;

        // if this learner is active or new, then do not continue
        if (in_array($learner['status'], ['new', 'active'])){
            return;
        }

        // we are only processing  inactive or deleted learners

        // get the company licence for this site
        $licence = $serviceManager->get("Licence\Company\Current");
        $validFrom = $licence['valid_from'];
        $validTo = $licence['valid_to'];
        $status = $licence['status'];
        $numLearners = $licence['num_learners'];

        // get the site details
        $site = $serviceManager->get('Site');

        // if licence status is null/false or inactive/expired/deleted or validFrom and validTo is null/false, do not continue
        if (!$status || in_array($status, [ 'inactive', 'expired', 'deleted' ]) || !($validFrom && $validTo)) {
            return ;
        }

        // get the available number of learners in the licence
        $availableNumLearners = $licence['num_learners'] - $site['num_learners'];

        // if the number of learners to activate (1) exceeds the number of available learner records, then do not continue;
        if ($availableNumLearners < $numUploadLearners) {
            $difference = $numUploadLearners - $availableNumLearners;
            throw new Exception\DomainException('There are no available spaces to add learners in the platform. The learner you are trying to activate will exceeds the licence by 1 learner.', LearnerEvent::ERROR_CODE_MAX_REACHED);
        }

    }

    /**
     * Learner\Model\Model preSelect TABLEGATEWAYEVENT listener
     *
     * @param TableGatewayEvent $event
     */
    public function preSelectLearnerModelCollection (TableGatewayEvent $event)
    {
        /* @var $table \Learner\Model\Model */
        /* @var $select \Zend\Db\Sql\Select */

        $table = $event->getTarget();
        $tableName = $table->getTable();
        $select = $event->getParam('select');

        /* @formatter:off */

        // filter only non-deleted learners
        $select->where([ "{$tableName}.status" => [ 'new', 'active', 'inactive' ] ]);

        // join with employment table
        $select->join('employment', "employment.user_id = {$tableName}.user_id", [
            'employment_id',
            'employment_type',
            'employment_manager' => 'manager',
            'employment_position' => 'position',
            'employment_start_date' => 'start_date',
            'employment_end_date' => 'end_date',
            'employment_cost_centre' => 'cost_centre'
        ], $select::JOIN_LEFT);

        // join with other tables
        $select->join('country', 'country.code = user.country OR country.name = user.country', [
            'country' => new Expression("COALESCE({$tableName}.country, country.name)")
        ], $select::JOIN_LEFT);
        $select->join('state', '(state.code = user.state OR state.name = user.state) AND state.country_code = country.code', [
            'state' => new  Expression("COALESCE({$tableName}.state, state.name)")
        ], $select::JOIN_LEFT);

        // order
        $select->order("first_name, last_name");

        // group
        $select->group("{$tableName}.user_id");

        /* @formatter:on */

        $event->setParam('select', $select);
    }
}