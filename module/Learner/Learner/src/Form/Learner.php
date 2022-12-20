<?php

namespace Learner\Form;

use Savve\Form\AbstractForm;

class Learner extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // user_id : hidden
        $this->add([
            'name' => 'user_id',
            'type' => 'Hidden'
        ]);

        // first_name : text
        $this->add([
            'name' => 'first_name',
            'type' => 'Text',
            'options' => [
                'label' => 'First name'
            ],
            'attributes' => [
                'placeholder' => 'Enter first or given name'
            ]
        ]);

        // last_name : text
        $this->add([
            'name' => 'last_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Last name'
            ],
            'attributes' => [
                'placeholder' => 'Enter last name or surname'
            ]
        ]);

        // email : text
        $this->add([
            'name' => 'email',
            'type' => 'Text',
            'options' => [
                'label' => 'Email address',
                'label_attributes' => array(
                    'id' => 'email-label'
                )
            ],
            'attributes' => [
                'placeholder' => 'Enter a unique email address'
            ]
        ]);

        // telephone : text
        $this->add([
            'name' => 'telephone',
            'type' => 'Text',
            'options' => [
                'label' => 'Telephone'
            ],
            'attributes' => [
                'placeholder' => 'Enter a telephone number'
            ]
        ]);

        // mobile_number : text
        $this->add([
            'name' => 'mobile_number',
            'type' => 'Text',
            'options' => [
                'label' => 'Mobile number'
            ],
            'attributes' => [
                'placeholder' => 'Enter a unique mobile number'
            ]
        ]);

        // street_address : text
        $this->add([
            'name' => 'street_address',
            'type' => 'Text',
            'options' => [
                'label' => 'Street address'
            ],
            'attributes' => [
                'placeholder' => 'Enter a street address'
            ]
        ]);

        // suburb : text
        $this->add([
            'name' => 'suburb',
            'type' => 'Text',
            'options' => [
                'label' => 'Suburb'
            ],
            'attributes' => [
                'placeholder' => 'Enter a suburb, town or city'
            ]
        ]);

        // postcode : text
        $this->add([
            'name' => 'postcode',
            'type' => 'Text',
            'options' => [
                'label' => 'Postcode'
            ],
            'attributes' => [
                'placeholder' => 'Enter postcode'
            ]
        ]);

        // group : select
        $this->add([
            'name' => 'group_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Group',
                'empty_option' => 'Select group',
                'value_options' => [
                ]
            ]
        ]);

        // cpd_id : text
        $this->add([
            'name' => 'cpd_id',
            'type' => 'Text',
            'options' => [
                'label' => 'CPD Identifier'
            ],
            'attributes' => [
                'placeholder' => 'Please enter your CPD Identifier',
                'readonly' => 'readonly'
            ]
        ]);

        // cpd_number : text
        $this->add([
            'name' => 'cpd_number',
            'type' => 'Text',
            'options' => [
                'label' => 'CPD Number'
            ],
            'attributes' => [
                'placeholder' => 'Please enter your CPD number',
                'readonly' => 'readonly'
            ]
        ]);

        // referrer : select
        $this->add([
            'name' => 'referrer',
            'type' => 'Select',
            'options' => [
                'label' => 'How did you here about us?',
                'empty_option' => 'Select a referral',
                'value_options' => [
                    'website' => 'Website',
                    'news article' => 'News article',
                    'google' => 'Google',
                    'advertisement' => 'Advertisement',
                    'referral' => 'Referral',
                    'other' => 'Other'
                ]
            ]
        ]);

        // note : text
        $this->add([
            'name' => 'note',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Interests'
            ],
            'attributes' => [
                'placeholder' => 'Please indicate the subjects you are most interested in learning about',
                'cols' => 30,
                'rows' => 5
            ]
        ]);

        // state : text
        $this->add([
            'name' => 'state',
            'type' => 'Text',
            'options' => [
                'label' => 'State'
            ],
            'attributes' => [
                'placeholder' => 'Enter a state, county or province'
            ]
        ]);

        // subscription : checkbox
        $this->add([
            'name' => 'subscription',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Subscribe',
                'checked_value' => 1,
                'unchecked_value' => 0
            ],
            'attributes' => [
                'placeholder' => 'Click here to subscribe to our newsletter'
            ]
        ]);

        // country : select
        $this->add([
            'name' => 'country',
            'type' => 'Select',
            'options' => [
                'label' => 'Country',
                'empty_option' => 'Select country',
                'value_options' => [
                    'AU' => 'Australia',
                    'NZ' => 'New Zealand',
                    'US' => 'United States of America'
                ]
            ]
        ]);

        // gender : select
        $this->add([
            'name' => 'gender',
            'type' => 'Select',
            'options' => [
                'label' => 'Gender',
                'empty_option' => 'Select gender',
                'value_options' => [
                    'female' => 'Female',
                    'male' => 'Male'
                ]
            ]
        ]);

        // status : select
        $this->add([
            'name' => 'status',
            'type' => 'Select',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'new' => 'New',
                    'active' => 'Active',
                    'disabled' => 'Disable',
                    'deleted' => 'Delete'
                ]
            ]
        ]);

        // timezone : select
        $this->add([
            'name' => 'timezone',
            'type' => 'Select',
            'options' => [
                'label' => 'Timezone',
                'empty_option' => 'Select timezone',
                'value_options' => [
                    'Australia/Sydney' => 'Australia/Sydney',
                    'Australia/Melbourne' => 'Australia/Melbourne',
                    'Australia/Brisbane' => 'Australia/Brisbane',
                    'Australia/Canberra' => 'Australia/Canberra',
                    'Australia/Darwin' => 'Australia/Darwin',
                    'Australia/Adelaide' => 'Australia/Adelaide',
                    'Australia/Perth' => 'Australia/Perth',
                    'Australia/Hobart' => 'Australia/Hobart'
                ]
            ]
        ]);

        // locale : select
        $this->add([
            'name' => 'locale',
            'type' => 'Select',
            'options' => [
                'label' => 'Language',
                'empty_option' => 'Select language',
                'value_options' => [
                    'en_AU' => 'English/Australia',
                    'en_NZ' => 'English/New Zealand'
                ]
            ]
        ]);

        // new_password : password
        $this->add([
            'name' => 'new_password',
            'type' => 'Password',
            'attributes' => [
                'type' => 'password',
                'placeholder' => 'Provide a password between 6 and 32 characters in length'
            ],
            'options' => [
                'label' => 'Password'
            ]
        ]);

        // confirm_password : password
        $this->add([
            'name' => 'confirm_password',
            'type' => 'Password',
            'attributes' => [
                'type' => 'password',
                'placeholder' => 'Confirm unique password'
            ],
            'options' => [
                'label' => 'Confirm password'
            ]
        ]);

        // password_token : hidden
        $this->add([
            'name' => 'password_token',
            'type' => 'Text',
            'attributes' => [
                'placeholder' => 'Enter unique password token'
            ],
            'options' => [
                'label' => 'Password Token'
            ]
        ]);

        // identity : text
        $this->add([
            'name' => 'identity',
            'type' => 'Text',
            'options' => [
                'label' => 'Email address, mobile number or employment ID'
            ],
            'attributes' => [
                'placeholder' => 'Enter your email address, mobile number or employment ID'
            ]
        ]);

        // employment_id : text
        $this->add([
            'name' => 'employment_id',
            'type' => 'Text',
            'options' => [
                'label' => 'Employment ID'
            ],
            'attributes' => [
                'placeholder' => 'Enter your employment ID'
            ]
        ]);

        // agent_code
        $this->add([
            'name' => 'agent_code',
            'type' => 'Text',
            'options' => [
                'label' => 'Agency Code'
            ],
            'attributes' => [
                'placeholder' => 'Enter the agents code'
            ]
        ]);

        // agent_password
        $this->add([
            'name' => 'agent_password',
            'type' => 'Text',
            'options' => [
                'label' => 'Agency Password'
            ],
            'attributes' => [
                'placeholder' => 'Enter a password'
            ]
        ]);

        // agent_email
        $this->add([
            'name' => 'agent_email',
            'type' => 'Text',
            'options' => [
                'label' => 'CC Email'
            ],
            'attributes' => [
                'placeholder' => 'Enter a CC email address'
            ]
        ]);

        // start date
        $this->add([
            'name' => 'start_date',
            'type' => 'Datetime',
            'options' => [
                'label' => 'Start Date'
            ],
            'attributes' => [
                'placeholder' => 'Select a course start data'
            ]
        ]);

        // course selector
        $this->add([
            'name' => 'course_selector',
            'type' => 'Select',
            'options' => [
                'label' => 'Course Selector',
                'empty_option' => 'Select a course',
                'value_options' => [
                //    '101' => 'Course 101',
                 //   '102' => 'Course 102'
                ]
            ]
        ]);
    }
}