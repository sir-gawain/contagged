<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('contagged', 'pi1/class.tx_contagged_pi1.php', '_pi1', 'list_type', 1);
?>
