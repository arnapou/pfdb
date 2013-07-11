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
 * @param \Arnapou\PFDB\Table $table 
 */
function print_table($title, $table) {
	echo '<strong>' . $title . '</strong>';
	echo '<table style="background:#aaa">';
	$first = true;
	foreach ( $table as $key => $row ) {
		if ( is_object($row) ) {
			if ( !isset($methods) ) {
				$methods = array_filter(get_class_methods($row), function($val) {
						return 0 === strpos($val, 'get');
					});
			}
			// TH
			if ( $first ) {
				echo '<tr>';
				echo '<th style="padding:0 4px;background:#ddd">-key-</th>';
				foreach ( $methods as $method ) {
					echo '<th style="padding:0 4px;background:#ddd">' . $method . '()</th>';
				}
				echo '</tr>';
				$first = false;
			}
			// TD
			echo '<tr>';
			echo '<td style="padding:0 4px;background:#fff">' . $key . '</td>';
			foreach ( $methods as $method ) {
				echo '<td style="padding:0 4px;background:#fff">' . $row->$method() . '</td>';
			}
			echo '</tr>';
		}
		else {
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
	}
	echo '</table>';
	echo '<br />';
}