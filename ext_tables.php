<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_contagged_terms');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_contagged_terms');

// add contagged to the "insert plugin" content element
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:contagged/locallang_db.php:tx_contagged_terms.plugin', $_EXTKEY . '_pi1'), 'list_type', 'contagged');

// initialize static extension templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'static/', 'Content parser');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'static/examples/', 'Experimental Setup');


// Add a field  "exclude this page from parsing" to the table "pages" and "tt_content"
$tempColumns = Array(
	"tx_contagged_dont_parse" => Array(
		"exclude" => 1,
		"label" => "LLL:EXT:contagged/locallang_db.xml:pages.tx_contagged_dont_parse",
		"config" => Array(
			"type" => "check",
		)
	),
);

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('contagged') . 'tx_contagged_userfunction.php');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("pages", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("pages", "tx_contagged_dont_parse;;;;1-1-1");

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_content", "tx_contagged_dont_parse;;;;1-1-1");

?>
