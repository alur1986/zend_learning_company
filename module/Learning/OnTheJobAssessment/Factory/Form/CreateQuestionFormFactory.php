<?php

namespace OnTheJobAssessment\Factory\Form;

use \ArrayObject as Object;
use OnTheJobAssessment\InputFilter\QuestionInputFilter;
use OnTheJobAssessment\InputFilter\QuestionsInputFilter as InputFilter;
use OnTheJobAssessment\Hydrator\AggregateHydrator as Hydrator;
use OnTheJobAssessment\Form\QuestionsForm as Form;
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
     //   $routeMatch = $serviceManager->get('Application')
     //       ->getMvcEvent()
     //       ->getRouteMatch();
     //   $siteId = $routeMatch->getParam('site_id');

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // add inputFilter for form element "questions"
        $questionsInputFilter = new QuestionInputFilter();
        $collectionsInputFilter = new CollectionInputFilter();
        $collectionsInputFilter->setInputFilter($questionsInputFilter);
        $inputFilter->add($collectionsInputFilter, 'questions');

        // form
        $form = new Form('create-on-the-job-assessment-questions');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'activity_id',
            'assessment_id',
            'introduction',
            'assessor_comments',
            'show_score',
            'questions' => [
                'question_id',
                'question',
                'sort_order'
            ],
            'question_header',
            'column_headers'
        ]);

        return $form;
    }
}