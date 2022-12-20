<?php

namespace Report\MyExperience\Hydrator;

use Savve\Stdlib\Hydrator;
use Savve\Stdlib\Hydrator\Strategy;
use Savve\Stdlib\Hydrator\Filter;
use Zend\Stdlib\Hydrator as ZendHydrator;
use Zend\Stdlib\Hydrator\Strategy as ZendHydratorStrategy;
use Zend\Stdlib\Hydrator\Filter as ZendHydratorFilter;
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator as ZendAggregateHydrator;

class AggregateHydrator extends ZendAggregateHydrator
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // strategies
        $emptyStringStrategy = new Strategy\EmptyString();
        $byteStringStrategy = new Strategy\ByteString();
        $booleanStrategy = new Strategy\Boolean();
        $integerStrategy = new Strategy\Integer();
        $dateTimeStrategy = new Strategy\DateTime();
        $strToLowerStrategy = new Strategy\StrToLower();
        $strToUpperStrategy = new Strategy\StrToUpper();
        $ucWordsStrategy = new Strategy\UcWords();

        // filters
        $excludeFilter = new Filter\Exclude();

        // hydrators
        $hydrator = new ZendHydrator\ArraySerializable();
        $hydrator->addStrategy('*', $emptyStringStrategy);
        $this->add($hydrator);
    }
}