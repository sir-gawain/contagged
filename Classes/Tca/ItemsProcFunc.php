<?php

namespace Extrameile\Contagged\Tca;

/**
 * This file is part of the "contagged" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

/**
 * Userfunc to render alternative label for media elements
 */
class ItemsProcFunc
{
    public function user_addTermTypes(&$params, $pObj)
    {
        $template = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TemplateService::class);
        $template->tt_track = 0;
        $template->init();
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $this->getPageId($params['row']['pid']))->get();
        $rootlineIndex = 0;
        foreach ($rootline as $index => $rootlinePart) {
            if ($rootlinePart['is_siteroot'] == 1) {
                $rootlineIndex = $index;
                break;
            }
        }
        $template->runThroughTemplates($rootline, $rootlineIndex);
        $template->generateConfig();
        $conf = $template->setup['plugin.']['tx_contagged.'];

        // make localized labels
        $LOCAL_LANG_ARRAY = [];
        if (!empty($conf['types.'])) {
            foreach ($conf['types.'] as $typeName => $typeConfigArray) {
                unset($LOCAL_LANG_ARRAY);
                if (!$typeConfigArray['hideSelection'] > 0 && !$typeConfigArray['dataSource']) {
                    if (is_array($typeConfigArray['label.'])) {
                        foreach ($typeConfigArray['label.'] as $langKey => $labelText) {
                            $LOCAL_LANG_ARRAY[$langKey]['label'] = $labelText;
                        }
                    }
                    $LOCAL_LANG_ARRAY['default']['label'] = $typeConfigArray['label'] ? $typeConfigArray['label'] : $typeConfigArray['label.']['default'];
                    $params['items'][] = array($GLOBALS['LANG']->getLLL('label', $LOCAL_LANG_ARRAY), substr($typeName, 0, -1));
                }
            }
        }
    }

    /**
     * Get page id, if negative, then it is a "after record"
     *
     * @param int $pid
     * @return int
     */
    protected function getPageId($pid)
    {
        $pid = (int)$pid;
        if ($pid > 0) {
            return $pid;
        }
        $row = BackendUtility::getRecord('tt_content', abs($pid), 'uid,pid');
        return $row['pid'];
    }
}
