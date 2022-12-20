<?php

return [
	/**
	 * CONTROLLERS
	 */
	'controllers' => [
		'factories' => [
			'Elucidat\Controller\Manage' => 'Elucidat\Controller\ManageControllerFactory',
			'Elucidat\Controller\Author' => 'Elucidat\Controller\AuthorControllerFactory',
			'Elucidat\Controller\Launch' => 'Elucidat\Controller\LaunchControllerFactory',
		]
	],

	'service_manager' => [
		'factories' => [
			'Elucidat\Elucidat'         => 'Elucidat\Elucidat\Factory\Service\ElucidatServiceFactory',
			'Elucidat\Elucidat\Options' => 'Elucidat\Elucidat\Factory\Service\OptionsServiceFactory',

			'Elucidat\AllElucidatLicences'                 => 'Elucidat\Factory\Service\AllElucidatLicencesFactory',
			'Elucidat\PaidElucidatLicences'                => 'Elucidat\Factory\Service\PaidElucidatLicencesFactory',
			'Elucidat\TrialElucidatLicences'               => 'Elucidat\Factory\Service\TrialElucidatLicencesFactory',
			'Elucidat\SavvecentralElucidatLicencesFactory' => 'Elucidat\Factory\Service\SavvecentralElucidatLicencesFactory',
		]
	], /**
	 * FORM ELEMENTS
	 */
	'form_elements'   => [
		'factories'       => [
			'Elucidat\Form\Create' => 'Elucidat\Factory\Form\CreateElucidatFormFactory',
			'Elucidat\Form\Update' => 'Elucidat\Factory\Form\UpdateElucidatFormFactory',
			'Elucidat\Form\Link'   => 'Elucidat\Factory\Form\LinkElucidatFormFactory',
			'Elucidat\Form\Unlink' => 'Elucidat\Factory\Form\UnlinkElucidatFormFactory',

			'Elucidat\Form\CreateUser' => 'Elucidat\Factory\Form\CreateElucidatUserFormFactory',
			'Elucidat\Form\UpdateUser' => 'Elucidat\Factory\Form\LinkElucidatUserFormFactory',
			'Elucidat\Form\LinkUser'   => 'Elucidat\Factory\Form\LinkElucidatUserFormFactory',
			'Elucidat\Form\UnlinkUser' => 'Elucidat\Factory\Form\LinkElucidatUserFormFactory',

		], 'initializers' => [
			'Elucidat\Form\Initialiser'
		]
	],

	'view_helpers' => [
		'invokables' => [
			'elucidatLaunchLink' => 'Elucidat\View\Helper\ElucidatLaunchLink',
			'elucidatAccess' => 'Elucidat\View\Helper\ElucidatAccess'
		]
	]

];