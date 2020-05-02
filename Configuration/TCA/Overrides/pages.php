<?php

call_user_func(function (){
    $tempColumns = array(
        "tx_contagged_dont_parse" => array(
            "exclude" => 1,
            "label" => "LLL:EXT:contagged/locallang_db.xml:pages.tx_contagged_dont_parse",
            "config" => array(
                "type" => "check",
            )
        ),
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("pages", $tempColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("pages", "tx_contagged_dont_parse;;;;1-1-1");
});
