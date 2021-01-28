# VuDL Statistical Tools

## Background

Villanova uses the Piwik/Matomo tool to collect usage statistics for its Digital Library. This repository
contains tools that can apply collection-specific filtering to Piwik/Matomo data for specific insights into
usage of the repository.

## Initial Setup

To get started, copy `config.php.dist` to `config.php`, and edit the constants within to appropriate values.

## Usage

1. Export the desired statistics from Piwik/Matomo to a CSV file. Ensure that the file is truly CSV; sometimes
the Piwik/Matomo CSV is actually a tab-delimited text file, which may cause problems for these scripts. In that
case, you can use Microsoft Excel to reformat it to a true CSV by importing the data and using "Save As." The
final CSV file should be placed in the same directory with these scripts, and should be named to match the
`PIWIK_STATS_CSV` setting in `config.php`.

2. Run the `collect-pids.php` script to gather PID data from Solr.

3. Run the `filter-pids.php` script to create filtered reports.