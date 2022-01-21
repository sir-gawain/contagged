<?php
/***************************************************************
 *  Copyright notice
 *  (c) 2007 Jochen Rau <j.rau@web.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Aks\Contagged\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * The model of contagged.
 *
 * @author    Jochen Rau <j.rau@web.de>
 * @package    TYPO3
 * @subpackage    tx_contagged_model_mapper
 */
class Tx_contagged_model_mapper implements \TYPO3\CMS\Core\SingletonInterface
{
    private $conf; // the TypoScript configuration array
//    private $controller;
    /** @var ContentObjectRenderer  */
    private $cObj;

    public function __construct($controller)
    {
//        $this->controller = $controller;
        $this->conf       = $controller->conf;
        if ( ! is_object($this->cObj)) {
            $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        }
    }

    /**
     * Build an array of the entries in the specified table
     *
     * @param array  $result : An result pointer of the database query
     * @param string $dataSource : The identifier of the data source
     *
     * @return   array         An array with the data of the table
     */
    public function getDataArray($result, $dataSource)
    {
        $dataArray             = [];
        $dataSourceConfigArray = $this->conf['dataSources.'][$dataSource . '.'];

        // add additional fields configured in the mapping configuration of the data source
        $fieldsToMapArray = [];
        foreach ($dataSourceConfigArray['mapping.'] as $fieldToMap => $value) {
            $fieldsToMapArray[] = substr($fieldToMap, 0, -1);
        }
        $fieldsToMapfromTS = GeneralUtility::trimExplode(',', $this->conf['fieldsToMap'], 1);
        foreach ($fieldsToMapfromTS as $key => $fieldToMap) {
            if ( ! in_array($fieldToMap, $fieldsToMapArray, true)) {
                $fieldsToMapArray[] = $fieldToMap;
            }
        }

        // iterate through all data from the datasource
        foreach ($result as $row) {
            $termMain                  = $dataSourceConfigArray['mapping.']['term_main.']['field'] ? $dataSourceConfigArray['mapping.']['term_main.']['field'] : '';
            $termReplace               = $dataSourceConfigArray['mapping.']['term_replace.']['field'] ? $dataSourceConfigArray['mapping.']['term_replace.']['field'] : '';
            $term                      = $row[$termReplace] ? $row[$termReplace] : $row[$termMain];
            $mappedDataArray           = [];
            $mappedDataArray['term']   = $term;
            $mappedDataArray['source'] = $dataSource;
            foreach ($fieldsToMapArray as $field) {
                $value = $dataSourceConfigArray['mapping.'][$field . '.'];
                if ($value['value']) {
                    $mappedDataArray[$field] = $value['value'];
                } elseif ($value['field']) {
                    $mappedDataArray[$field] = $row[$value['field']];
                } else {
                    $mappedDataArray[$field] = null;
                }
                if ($value['stdWrap.']) {
                    $mappedDataArray[$field] = $this->cObj->stdWrap($mappedDataArray[$field], $value['stdWrap.']);
                }
                if ($field === 'link') {
                    $mappedDataArray[$field . '.']['additionalParams'] = $value['additionalParams'];
                    if ($value['additionalParams.']['stdWrap.']) {
                        $mappedDataArray[$field . '.']['additionalParams'] = $this->cObj->stdWrap($mappedDataArray[$field . '.']['additionalParams'], $value['additionalParams.']['stdWrap.']);
                    }
                }
                $GLOBALS['TSFE']->register['contagged_' . $field] = $mappedDataArray[$field];
            }

            // post processing
            $mappedDataArray['term_alt'] = GeneralUtility::trimExplode(chr(10), $row['term_alt'], 1);
            // TODO: hook "mappingPostProcessing"

            if ( ! empty($dataSourceConfigArray['mapping.']['uid.']['field'])) {
                $dataArray[$row[$dataSourceConfigArray['mapping.']['uid.']['field']]] = $mappedDataArray;
            } else {
                $dataArray[] = $mappedDataArray;
            }
        }

        return $dataArray;
    }
}
