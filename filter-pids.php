<?php
/**
 * Filter tool for VuDLStats package.
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

// Main routine: for each PIDS file, create a filtered report:
foreach (glob('pids-*.csv') as $file) {
	echo "Processing $file...\n";
	createFilteredReport($file);
}

/**
 * Given a file containing PIDs, generate a filtered report.
 *
 * @param string $filename Name of PID file.
 *
 * @return void
 */
function createFilteredReport($filename) {
	$pids = array_map('trim', file($filename));

	$in = fopen(PIWIK_STATS_CSV, 'r');
	$out = fopen(str_replace('pids-', 'report-', $filename), 'w');
	$header = fgets($in);
	fputs($out, $header);
	while ($line = fgets($in)) {
		if (preg_match('/vudl:\d+/', $line, $matches)) {
			if (in_array($matches[0], $pids)) {
				fputs($out, $line);
			}
		}
	}
	fclose($in);
	fclose($out);
}
