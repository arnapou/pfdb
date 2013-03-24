<?php

/**
 * Prints a HTML title
 *
 * @param string $title 
 */
function print_title($title) {
	echo '<h1>' . $title . '</h1>';
}

/**
 * Prints a HTML table of the Table object
 *
 * @param string $title
 * @param \PFDB\Table $table 
 */
function print_table($title, $table) {
	echo '<strong>' . $title . '</strong>';
	echo '<table style="background:#aaa">';
	$first = true;
	foreach ( $table as $key => $row ) {
		// TH
		if ( $first ) {
			echo '<tr>';
			echo '<th style="padding:0 4px;background:#ddd">-key-</th>';
			foreach ( $row as $field => $value ) {
				echo '<th style="padding:0 4px;background:#ddd">' . $field . '</th>';
			}
			echo '</tr>';
			$first = false;
		}
		// TD
		echo '<tr>';
		echo '<td style="padding:0 4px;background:#fff">' . $key . '</td>';
		foreach ( $row as $field => $value ) {
			echo '<td style="padding:0 4px;background:#fff">' . $value . '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '<br />';
}