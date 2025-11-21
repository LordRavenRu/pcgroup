<?php
/**
 * @author     Vitaliy Skripka <contact@lordraven.ru>
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/formatter.php');

class PPAppPCGroup extends PPApp
{
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$enabled = JComponentHelper::isEnabled('com_phocacart');

			if (!$enabled) {
				$exists = false;
				return false;
			}

			$file = JPATH_ADMINISTRATOR . '/components/com_phocacart/phocacart.php';

			if (!JFile::exists($file)) {
				$exists = false;

				return $exists;
			}

			$exists = true;
		}

		return $exists;
	}


	private function assignGroup($userId, $points)
	{

		$db = PP::db();

		$query = 'INSERT INTO ' . $db->qn('#__phocacart_item_groups')
				. ' (`item_id`, `group_id`, `product_id`, `type`)'
				. ' VALUES (' . $db->Quote($userId) . ', ' . $db->Quote($points) . ',0,1)';

		$db->setQuery($query);
		$db->query();
	
		return;
	}

	private function removeGroup($userId, $points)
	{

		$db = PP::db();

		$query = 'DELETE FROM ' . $db->qn('#__phocacart_item_groups').' WHERE item_id='. $db->Quote($userId).' AND group_id='. $db->Quote($points).' AND product_id=0 AND type=1';
		$db->setQuery($query);
		$db->query();
	
		return;
	}

	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventname = '')
	{
		$exists = $this->exists();

		return $exists;
	}

	/**
	 * Triggered when a new subscription is purchaseed
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if (($new->isNotActive()) || ($prev != null && $prev->getStatus() == $new->getStatus())) {
			return true;
		}

		if (!$this->exists()) {
			return true;
		}

		$params = $this->getAppParams();
		$points = $params->get('group');

		if ($new->isActive() && $points) {
			$this->assignGroup($new->getBuyer()->getId(), $points);
		}
		if (!$new->isActive() && $points) {
			$this->removeGroup($new->getBuyer()->getId(), $points);
		}
		
		return true;
	 }	
}