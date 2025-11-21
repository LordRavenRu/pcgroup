<?php
/**
 * @author     Vitaliy Skripka <contact@lordraven.ru>
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Unauthorized Access');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayPlansPCGroup extends PPPlugins
{
}