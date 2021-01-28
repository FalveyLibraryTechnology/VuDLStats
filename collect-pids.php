<?php
/**
 * PID collector for VuDLStats package.
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2021.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuDL
 * @package  Stats
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/FalveyLibraryTechnology/VuDLStats/
 */

require_once 'config.php';

// Get a list of collections:
$getCollectionsQuery = 'hierarchy_parent_id:"' . TOP_LEVEL_PID . '" AND (modeltype_str_mv:"vudl-system:FolderCollection")';
$collections = solrGet($getCollectionsQuery, 'id,title');

// For each collection, store a list of PIDs:
foreach (array_map('trim', explode("\n", $collections)) as $row) {
	$parts = explode(',', $row, 2);
	$pid = $parts[0];
	if (substr($pid, 0, 4) !== 'vudl') {
		continue;
	}
	$title = $parts[1];
	echo "Downloading $title pids...\n";
	$pids = getCollectionPids($pid);
	writeCollectionPids($pids, $title);
}

/**
 * Perform a Solr query and return CSV-formatted results.
 *
 * @param string $query Solr query
 * @param string $fl    Field list
 *
 * @return string
 */
function solrGet($query, $fl = 'id') {
	return file_get_contents(
		SOLR_QUERY_URL . '?q=' . urlencode($query) . '&fl=' . urlencode($fl) . '&wt=csv&rows=1000000'
	);
}

/**
 * Given a PID, collect all child PIDs from Solr.
 *
 * @param string $pid Collection PID
 *
 * @return array
 */
function getCollectionPids($pid) {
    $query = 'hierarchy_all_parents_str_mv:"' . $pid . '" AND (modeltype_str_mv:"vudl-system:FolderCollection" OR modeltype_str_mv:"vudl-system:ResourceCollection")';
	$pids = explode("\n", solrGet($query));
	$filter = function ($pid) {
		return substr($pid, 0, 4) === 'vudl';
	};
	return array_filter(array_map('trim', $pids), $filter);
}

/**
 * Store a list of PIDs in an appropriately-named file.
 *
 * @param array  $pids  Array of PIDs
 * @param string $title Collection title for use in filename generation
 *
 * @return void
 */
function writeCollectionPids($pids, $title) {
	$filename = 'pids-' . preg_replace('/\W/', '-', $title) . '.csv';
	file_put_contents($filename, implode("\n", $pids));
}