<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        plugin.tx_contagged_pi1 >
        tt_content.list.20.contagged_pi1 = USER
        tt_content.list.20.contagged_pi1.userFunc = Extrameile\Contagged\Controller\ListController->main
    ');

    if (!class_exists('tx_contagged')) {
        class_alias('\Extrameile\Contagged\Service\Parser', 'tx_contagged');
    }
});
