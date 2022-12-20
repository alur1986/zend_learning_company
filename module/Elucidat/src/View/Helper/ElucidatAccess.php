<?php

namespace Elucidat\View\Helper;

use Savve\View\Helper\Html;

class ElucidatAccess extends Html
{
	/**
	 *  Site ID
	 */
	protected $siteId;

	/**
	 *  User ID
	 */
	protected $userId;

	/**
	 * Checks if the current 'active site' has an 'active' elucidat access or if the 'current user' has elucidat access enabled
	 * Provided 'context' determines the check to be completed - 'account' which uses the siteId or 'user' which uses the userId
	 *
	 * @param object $context
	 * @return boolean
	 */
	public function __invoke($context)
	{
		$access = false;
		if ($context) {

			try {
				$serviceLocator = $this->getServiceLocator();
				$serviceManager = $serviceLocator->getServiceLocator();
				$elucidatService = $serviceManager->get('Elucidat\Elucidat');

				$this->siteId 	= $this->params('site_id');
				$this->userId 	= $this->params("user_id");

				switch($context) {
					case "account":
						if ($this->siteId) {
							$result = $elucidatService->checkAccountAccess($this->siteId);
							if (isset($result['id']) && is_numeric($result['id'])) {
								$access = true;
								$this->accountId = $result['id'];
							}
						}
						break;

					case "user":
						if ($this->userId) {
							$result = $elucidatService->checkUserAccess($this->userId, $this->accountId);
							if (isset($result['id']) && is_numeric($result['id'])) {
								$access = true;
								$elucidatUserId = $result['id'];
							}
						}
						break;

					default:
						$access = false;
						break;
				}
				return $access;
			}
			catch (\Exception $e) {
				throw $e;
			}
		}
		return $access;
	}
}
