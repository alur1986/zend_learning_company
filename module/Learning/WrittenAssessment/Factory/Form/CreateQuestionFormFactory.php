<?php

namespace WrittenAssessment\Factory\Form;

use \ArrayObject as Object;
use WrittenAssessment\InputFilter\QuestionInputFilter;
use WrittenAssessment\InputFilter\QuestionsInputFilter as InputFilter;
use WrittenAssessment\Hydrator\AggregateHydrator as Hydrator;
use WrittenAssessment\Form\QuestionsForm as Form;
use Zend\InputFilter\CollectionInputFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateQuestionFormFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // add inputFilter for form element "questions"
        $questionsInputFilter = new QuestionInputFilter();
        $collectionsInputFilter = new CollectionInputFilter();
        $collectionsInputFilter->setInputFilter($questionsInputFilter);
        $inputFilter->add($collectionsInputFilter, 'questions');

        // form
        $form = new Form('create-written-assessment-questions');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'activity_id',
            'assessment_id',
            'introduction',
            'assessor_comments',
            'learner_comments',
            'pass_score',
            'show_score',
            'show_status',
            'questions' => [
                'question_id',
                'question',
                'sort_order'
            ]
        ]);

        return $form;
    }
}