<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
    tt_content.list.20.contagged_pi1 = USER
    tt_content.list.20.contagged_pi1.userFunc = Extrameile\Contagged\Controller\ListController->main
');
