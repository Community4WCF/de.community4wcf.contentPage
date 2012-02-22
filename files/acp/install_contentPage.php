<?php
/**
 * Sets group options.
 * 
 * @svn			$Id: C4WpagePage.class.php 1207 2010-04-09 23:32:46Z TobiasH87 $
 *
 * @author		Community4WCF
 * @copyright	2010 Community4WCF
 * @license		Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package		de.community4wcf.contentPage
 * @category	WoltLab Community Framework
 */

// get package id
$packageID = $this->installation->getPackageID();

// user, mod and admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID IN (4,5,6)
		AND optionID IN (
	SELECT	optionID
		FROM	wcf".WCF_N."_group_option
		WHERE	optionName LIKE 'user.contentPage.%'
			OR optionName LIKE 'mod.contentPage.%'
			AND packageID IN (
				SELECT	dependency
				FROM	wcf".WCF_N."_package_dependency
				WHERE	packageID = ".$packageID."
				)
		)
	AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);


?>