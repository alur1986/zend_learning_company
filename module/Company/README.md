COMPANY module
==========================================================

Requirements:
--------------------------------------
* Zend Framework 2 (https://github.com/zendframework/zf2.git)

Introduction
--------------------------------------
The COMPANY module is a Zend Framework 2 module for managing company data in the platform.

Features/Goals
--------------------------------------

### Functionality includes:
* add new company [COMPLETE
* edit company [COMPLETE]

Installation
--------------------------------------

### Main setup
1. Clone this repository and save under: module/Company by typing: `git clone https://savve@bitbucket.org/savve/savvecentral-module-company.git module/Company`
2. Execute the MySQL query located in the files `data\company.sql` and `data\company_contact.sql` of the module's directory.
3. Activate the module in your Zend Framework 2 `applications.config.php` file by adding the module's namespace just like so:

	<?php
	return array(
		// register your modules here
		'modules' => array(
			'Application',
			'Company'
		)
	);
