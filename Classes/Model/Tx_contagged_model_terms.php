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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The model of contagged.
 *
 * @author    Jochen Rau <j.rau@web.de>
 * @package    TYPO3
 * @subpackage    tx_contagged_model_terms
 */
class Tx_contagged_model_terms implements \TYPO3\CMS\Core\SingletonInterface
{

    private $conf; // the TypoScript configuration array
    private $controller;

    private $dataSourceArray = [];

    private $terms = [];

    private $configuredSources = [];

    private $listPagesCache = [];

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->conf = $controller->conf;
        if (!is_object($this->cObj)) {
            $this->cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        }

        $this->mapper = GeneralUtility::makeInstance(Tx_contagged_model_mapper::class, $this->controller);

        if (is_array($this->conf['dataSources.'])) {
            foreach ($this->conf['dataSources.'] as $dataSource => $sourceConfiguration) {
                $this->configuredSources[$sourceConfiguration['sourceName']] = substr($dataSource, 0, -1);
            }
        } else {
            throw new RuntimeException('No configuration. Please include the static template.');
        }

        $typesArray = $this->conf['types.'];
        foreach ($typesArray as $type => $typeConfigArray) {
            $storagePidsArray = $this->getStoragePidsArray($typeConfigArray);
            $dataSource = $typeConfigArray['dataSource'] ? $typeConfigArray['dataSource'] : 'default';
            foreach ($storagePidsArray as $pid) {
                // if there is an entry for the data source: check for duplicates before adding the pid
                // otherwise: create a new entry and add the pid
                if ($this->dataSourceArray[$dataSource]) {
                    if (!in_array($pid, $this->dataSourceArray[$dataSource])) {
                        $this->dataSourceArray[$dataSource][] = intval($pid);
                    }
                } else {
                    $this->dataSourceArray[$dataSource][] = intval($pid);
                }
            }
        }
    }

    public function findAllTerms($additionalWhereClause = '')
    {
        if (empty($this->terms)) {
            foreach ($this->dataSourceArray as $dataSource => $storagePidsArray) {
                $this->terms = array_merge($this->terms, $this->fetchTermsFromSource($dataSource, $storagePidsArray));
            }
        }
        return $this->terms;
    }

    public function findAllTermsToListOnPage($pid = null)
    {
        $terms = $this->findAllTerms(' AND exclude=0');
        if ($pid === null) {
            $pid = $GLOBALS['TSFE']->id;
        }
        $filteredTerms = [];
        foreach ($terms as $key => $term) {
            $typeConfigurationArray = $this->conf['types.'][$term['term_type'] . '.'];
            $listPidsArray = $this->getListPidsArray($term['term_type']);
            if (($typeConfigurationArray['dontListTerms'] == 0) && (in_array($pid, $listPidsArray) || is_array($GLOBALS['T3_VAR']['ext']['contagged']['index'][$pid][$key]))) {
                $filteredTerms[$key] = $term;
            }
        }
        uasort($filteredTerms, array($this, 'sortByTermAscending'));
        return $filteredTerms;
    }

    public function sortByTermAscending($termArrayA, $termArrayB)
    {
        return strnatcasecmp($termArrayA['term'], $termArrayB['term']);
    }

    public function findTermByUid($dataSource, $uid)
    {
        $terms = $this->fetchTermsFromSource($dataSource, [], $uid);
        if ($this->conf["fetchRelatedTerms"] == 1) {
            $this->fetchRelatedTerms($terms);
        }
        if (is_array($terms) && count($terms) > 0) {
            return array_shift($terms);
        } else {
            return null;
        }
    }

    /**
     * Build an array of the entries in the tables
     *
     * @param    string        $dataSource: The identifier of the data source
     * @param    array         $storagePids: An array of storage page IDs
     * @return   array         An array with the terms an their configuration
     */
    protected function fetchTermsFromSource($dataSource, $storagePidsArray = [], $uid = null)
    {
        $dataArray = [];
        $dataSourceConfigArray = $this->conf['dataSources.'][$dataSource . '.'];
        $tableName = $dataSourceConfigArray['sourceName'];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));

        $statement = $queryBuilder
            ->select('*')
            ->from($tableName);

        if (count($storagePidsArray)) {
            $statement->andWhere(
                $queryBuilder->expr()->in('pid', $storagePidsArray)
            );
        }

        if ($uid !== null) {
            $statement->andWhere(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            );
        }

        $result = $statement->execute()
            ->fetchAll();

        // map the fields
        $mappedResult = $this->mapper->getDataArray($result, $dataSource);

        if (is_array($mappedResult)) {
            foreach ($mappedResult as $result) {
                $dataArray[$result['source'] . '_' . $result['uid']] = $result;
            }
        }
        // TODO piVars as a data source
        return $dataArray;
    }

    protected function fetchRelatedTerms(&$dataArray)
    {
        $newDataArray = [];
        foreach ($dataArray as $key => $termArray) {
            $result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                'uid_foreign, tablenames', // SELECT ...
                'tx_contagged_related_mm', // FROM ...
                'uid_local=' . $termArray['uid'], // WHERE ..
                'sorting'
            );

            if (!empty($result)) {
                $termArray['related'] = [];
                foreach ($result as $row) {
                    $dataSource = $this->configuredSources[$row['tablenames']];
                    if ($dataSource !== null) {
                        $termArray['related'][] = array('source' => $dataSource, 'uid' => $row['uid_foreign']);
                    }
                }
            } else {
                $termArray['related'] = null;
            }
            $newDataArray[] = $termArray;
        }
        $dataArray = $newDataArray;
    }

    /**
     * get the storage pids; cascade: type > dataSource > globalConfig
     *
     * @param string    $typeConfigArray
     * @return array    An array containing the storage PIDs of the type given by
     * @author Jochen Rau
     */
    protected function getStoragePidsArray($typeConfigArray)
    {
        $storagePidsArray = [];
        $dataSource = $typeConfigArray['dataSource'] ? $typeConfigArray['dataSource'] : 'default';
        if (!empty($typeConfigArray['storagePids'])) {
            $storagePidsArray = GeneralUtility::intExplode(',', $typeConfigArray['storagePids']);
        } elseif (!empty($this->conf['dataSources.'][$dataSource . '.']['storagePids'])) {
            $storagePidsArray = GeneralUtility::intExplode(',', $this->conf['dataSources.'][$dataSource . '.']['storagePids']);
        } elseif (!empty($this->conf['storagePids'])) {
            $storagePidsArray = GeneralUtility::intExplode(',', $this->conf['storagePids']);
        }
        return $storagePidsArray;
    }

    /**
     * get the list page IDs; cascade: type > globalConfig
     *
     * @param string    $typeConfigArray
     * @return array    An array containing the list PIDs of the type given by
     * @author Jochen Rau
     */
    protected function getListPidsArray($termType)
    {
        if (!isset($this->listPagesCache[$termType])) {
            $listPidsArray = [];
            if (!empty($this->conf['types.'][$termArray['term_type'] . '.']['listPages'])) {
                $this->listPagesCache[$termType] = GeneralUtility::intExplode(',', $this->conf['types.'][$termArray['term_type'] . '.']['listPages']);
            } elseif (!empty($this->conf['listPages'])) {
                $this->listPagesCache[$termType] = GeneralUtility::intExplode(',', $this->conf['listPages']);
            }
        }
        return $this->listPagesCache[$termType];
    }
}
