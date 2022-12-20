<?php

namespace Elucidat\View\Helper;

use Savve\View\Helper\Html;

class ElucidatLaunchLink extends Html
{

    /**
     * HTML element tag
     *
     * @var string
     */
    protected $tag = 'a';

    /**
     * Class prefix
     *
     * @var string
     */
    protected $prefix = 'btn';

    /**
     * Valid tags
     *
     * @var array
     */
    protected $validTags = [
        'a',
        'span'
    ];

    /**
     * Invoke View Helper
     *
     * Sample usage:
     * <?php echo $this->buttonLink('Just label'); ?>
	 * <?php echo $this->buttonLink('Button with route', 'group/new'); ?>
	 * <?php echo $this->buttonLink('Button with route and class', 'group/new', 'btn-inverse'); ?>
	 * <?php echo $this->buttonLink('Button with URL', 'http://www.google.com/'); ?>
	 * <?php echo $this->buttonLink('Button with URL and class', 'http://www.google.com/', 'btn-inverse'); ?>
	 * <?php echo $this->buttonLink('New Group', 'group/new', 'btn-primary', ['data-modal-type' => 'ajax', 'class' => 'btn-mini']); ?>
	 * <?php echo $this->buttonLink('Edit Group', 'group/edit', ['group_id' => 100001], 'btn-primary', ['data-modal-type' => 'ajax', 'class' => 'btn-large']); ?>
	 *
     * @return ButtonLink
     */
    public function __invoke ($label, $href = '#')
    {

		$args = func_get_args();
		$argc = func_num_args();

		// init attributes array
		$attributes = [];

		// set the label
		// first args is always the label
		$this->setLabel($label);

		$serviceLocator  = $this->getServiceLocator();
		$serviceManager  = $serviceLocator->getServiceLocator();
		$elucidatService = $serviceManager->get('Elucidat\Elucidat');

		$userId = $this->params("user_id");

		$author = $elucidatService->findOneAuthorByUserId($userId);
		if(!$author || empty($author)){
			/** @var \TwitterBootstrap\View\Helper\Alert $alertHelper */
			$alertHelper = $serviceLocator->get('alert');
			return $alertHelper->__invoke("No Access",'info');
		}

		$elucidatEmail 			= $author['elucidat_email'];
		$account 				= $author['account'];
		$elucidatCustomerCode 	= $account['elucidat_customer_code'];
		$elucidatPublicKey 		= $account['elucidat_public_key'];
		$elucidatCompanyName 	= $account['company_name'];

		// generate the 'elucidat launch link' using the service
		$launchLink = $elucidatService->generateLaunchLink($elucidatEmail, $elucidatCompanyName, $elucidatPublicKey);

		$attributes['href'] = $launchLink;
		$this->eventParams['href'] = $href;

		// if the third args exists and is a string, then this is class
		if (isset($args[1])) {
			if(is_string($args[1])){
				$attributes['class'] = $args[1];
			}
			// else if it is an array, then it is an attributes array
			else {
				if (isset($args[1]['class'])) {
					$attributes['class'] = implode(' ', [ (isset($attributes['class']) ? $attributes['class'] : ''), $args[1]['class'] ]);
				}
				unset($args[1]['class']);
				$attributes = array_merge($attributes, $args[1]);
			}
		}

	    // if the fourth args exists
	    if (isset($args[2])) {
	        // if it is a string, then this is a class
	        if (is_string($args[2])) {
	        	$attributes['class'] = implode(' ', [ (isset($attributes['class']) ? $attributes['class'] : ''), $args[2] ]);
	        }

		    // else if it is an array, then it is an attributes array
		    else {
		        if (isset($args[2]['class'])) {
		            $attributes['class'] = implode(' ', [ (isset($attributes['class']) ? $attributes['class'] : ''), $args[2]['class'] ]);
		        }
		        unset($args[2]['class']);
		        $attributes = array_merge($attributes, $args[2]);
		    }
	    }

	    // if the fifth and last args exists, and is an array then this is an attribute array
	    if (isset($args[3]) && is_array($args[3])) {
	        // if there is a class key, then append that to the current class attribute
	        if (isset($args[3]['class'])) {
	            $attributes['class'] = implode(' ', [ (isset($attributes['class']) ? $attributes['class'] : ''), $args[4]['class'] ]);
	        }
	        unset($args[3]['class']);
	        $attributes = array_merge($attributes, $args[3]);
	    }

	    // set the attributes
	    $this->setAttributes($attributes);

        return $this;
    }

    /**
     * Return the composed HTML string
     *
     * @return string
     */
    public function toString ()
    {
		$html = parent::toString() . PHP_EOL;
        return $html;
    }
}