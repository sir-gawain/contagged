<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms',
        'label' => 'term_replace',
        'label_alt' => 'term_main, term_alt',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'useColumnsForDefaultValues' => 'term_type',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('contagged') . 'icon_tx_contagged_terms.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group term_main, term_alt, term_type, term_lang, replacement, desc_short, desc_long, reference, pronunciation, image, imagecaption, imagealt, imagetitle, multimedia, related, link, exclude',
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, term_main, term_alt, term_type, term_lang, term_replace, desc_short, desc_long;;;richtext[*]:rte_transform[mode=ts_css|imgpath=uploads/tx_contagged/rte/], reference, pronunciation, image, imagecaption, imagealt, imagetitle, multimedia, related, link, exclude'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime, endtime, fe_group'],
        '2' => ['showitem' => ''],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            ],
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_contagged_terms',
                'foreign_table_where' => 'AND tx_contagged_terms.pid=###CURRENT_PID### AND tx_contagged_terms.sys_language_uid IN (-1,0)',
                'default' => 0
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0',
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.any_login', -2],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.usergroups', '--div--'],
                ],
                'foreign_table' => 'fe_groups',
            ],
        ],
        'term_main' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_main',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ],
        ],
        'term_alt' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_alt',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'term_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'user_addTermTypes',
                'size' => 1,
                'maxitems' => 1,
                'disableNoMatchingValueElement' => 1,
            ],
        ],
        'term_lang' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                // TODO Make selectable languages configurable.
                'items' => [
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.0', ''],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.1', 'en'],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.2', 'fr'],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.3', 'de'],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.4', 'it'],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.5', 'es'],
                    ['LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_lang.I.6', 'un'],
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'term_replace' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.term_replace',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ],
        ],
        'desc_short' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.desc_short',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ],
        ],
        'desc_long' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.desc_long',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ],
        ],
        'reference' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.reference',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '2',
            ],
        ],
        'pronunciation' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.pronunciation',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ],
        ],
        'image' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.images',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/pics',
                'show_thumbs' => '1',
                'size' => 3,
                'autoSizeMax' => 15,
                'maxitems' => '99',
                'minitems' => '0',
            ],
        ],
        'imagecaption' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.imagecaption',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '3',
            ],
        ],
        'imagealt' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.imagealt',
            'config' => [
                'type' => 'text',
                'cols' => '20',
                'rows' => '3',
            ],
        ],
        'imagetitle' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.imagetitle',
            'config' => [
                'type' => 'text',
                'cols' => '20',
                'rows' => '3',
            ],
        ],
        'multimedia' => [
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.multimedia',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'swf,swa,dcr,wav,avi,au,mov,asf,mpg,wmv,mp3,mp4,m4v',
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/media',
                'size' => '2',
                'maxitems' => '1',
                'minitems' => '0',
            ],
        ],
        'related' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.related',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => '*',
                'MM' => 'tx_contagged_related_mm',
                'show_thumbs' => 1,
                'size' => 3,
                'autoSizeMax' => 20,
                'maxitems' => 9999,
                'minitems' => 0,
            ],
        ],
        'link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.link',
            'config' => [
                'type' => 'input',
                'size' => '28',
                'max' => '255',
                'checkbox' => '',
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        'icon' => 'actions-wizard-link',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'width=800,height=600,status=0,menubar=0,scrollbars=1'
                    ]
                ],
            ],
        ],
        'exclude' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:contagged/locallang_db.xml:tx_contagged_terms.exclude',
            'config' => [
                'type' => 'check',
            ],
        ],
    ],
];
