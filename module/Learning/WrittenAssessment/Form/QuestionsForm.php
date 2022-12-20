<?php

namespace WrittenAssessment\Form;

use Savve\Form\AbstractForm;

class QuestionsForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, $options = [])
    {
        parent::__construct($name, $options);

        // activity_id
        $this->add([
            'name' => 'activity_id',
            'type' => 'Hidden'
        ]);

        // assessment_id
        $this->add([
            'name' => 'assessment_id',
            'type' => 'Hidden'
        ]);

        // introduction
        $this->add([
            'name' => 'introduction',
            'type' => 'Textarea',
            'required' => true,
            'options' => [
                'label' => 'Introduction'
            ],
            'attributes' => [
                'placeholder' => 'Enter the introduction text that is shown to the learner and the assessor'
            ]
        ]);

        // assessor_comments
        $this->add([
            'name' => 'assessor_comments',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow assessor to include comments',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'checked' => 'checked'
            ]
        ]);

        // learner_comments
        $this->add([
            'name' => 'learner_comments',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow learner to include comments',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'checked' => 'checked'
            ]
        ]);

        // pass_score
        $this->add([
            'name' => 'pass_score',
            'type' => 'Text',
            'attributes' => [
                'placeholder' => 'Passing percentage'
            ],
            'options' => [
                'label' => 'Passing Percentage'
            ]
        ]);

        // proof_of_evidence
        $this->add([
            'name' => 'proof_of_evidence',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow learner to add proof of evidence',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'checked' => 'checked'
            ]
        ]);

        // show_score
        $this->add([
            'name' => 'show_score',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow assessor to set score',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'checked' => 'checked'
            ]
        ]);

        // show_status
        $this->add([
            'name' => 'show_status',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow assessor to set completion status',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'checked' => 'checked'
            ]
        ]);

        // questions
        $this->add(
                [
                    'name' => 'questions',
                    'type' => '\Zend\Form\Element\Collection',
                    'options' => [
                        'label' => 'Questions',
                        'count' => 1,
                        'should_create_template' => true,
                        'allow_add' => true,
                        'allow_remove' => false,
                        'target_element' => [
                            'type' => 'WrittenAssessment\Form\Fieldset\CreateQuestionFieldset',
                            'object' => 'Savvecentral\Entity\AssessmentQuestions'
                        ],
                        'fieldset_wrapper' => [
                            'data-add-button-text' => 'Add Question',
                            'data-remove-button-text' => 'Remove Last Question',
                            'data-add-button-class' => 'btn btn-primary btn-small',
                            'data-remove-button-class' => 'btn btn-warning btn-small',
                            'data-max' => 20,
                            'data-min' => 1,
                            'data-warning-min-show' => false,
                            'data-warning-min-reach' => 'You need to have at least 1 question',
                            'data-warning-max-reach' => 'You have reached the maximum number of questions (20 questions)',
                            'data-warning-element-removed' => 'You have removed a question. Click Save to add questions. Click Cancel Changes to revert back to the version you last saved.',
                            'data-sortable' => 1
                        ]
                    ],
                    'attributes' => [
                        'max-elements' => 20
                    ]
                ]);
    }
}