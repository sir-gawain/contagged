<?php

call_user_func(function (){
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_contagged_terms');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:contagged/locallang_db.php:tx_contagged_terms.plugin', 'contagged_pi1'), 'list_type', 'contagged');

    // Add a field  "exclude this page from parsing" to the table "pages" and "tt_content"
    $tempColumns = array(
        "tx_contagged_dont_parse" => array(
            "exclude" => 1,
            "label" => "LLL:EXT:contagged/Resources/Private/Language/locallang_db.xml:pages.tx_contagged_dont_parse",
            "config" => array(
                "type" => "check",
            )
        ),
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $tempColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_content", "tx_contagged_dont_parse;;;;1-1-1");
});
