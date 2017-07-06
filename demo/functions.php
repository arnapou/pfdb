<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Prints a HTML title
 *
 * @param string $title
 */
function print_title($title)
{
//    echo '<h1>' . $title . '</h1>';
}

/**
 * Prints a HTML table of the Table object
 *
 * @param string              $title
 * @param \Arnapou\PFDB\Table $table
 */
function print_table($title, $table)
{
    $func = null;
    if (is_callable($table)) {
        $func = $table;
        $table = $func();
    }
    echo '<h4>' . $title . '</h4>';
    echo '<div class="row">';
    echo '<div class="col-lg-6">';
    echo '<table class="table" style="table-layout:fixed;">';
    $first = true;
    foreach ($table as $key => $row) {
        if (is_object($row)) {
            if (!isset($methods)) {
                $methods = array_filter(get_class_methods($row), function ($val) {
                    return 0 === strpos($val, 'get');
                });
            }
            // TH
            if ($first) {
                echo '<tr>';
                echo '<th style="padding:0 4px;background:#ddd">-key-</th>';
                foreach ($methods as $method) {
                    echo '<th style="padding:0 4px;background:#ddd">' . $method . '()</th>';
                }
                echo '</tr>';
                $first = false;
            }
            // TD
            echo '<tr>';
            echo '<td style="padding:0 4px;background:#fff">' . $key . '</td>';
            foreach ($methods as $method) {
                echo '<td style="padding:0 4px;background:#fff">' . $row->$method() . '</td>';
            }
            echo '</tr>';
        } else {
            // TH
            if ($first) {
                echo '<tr>';
                echo '<th style="padding:0 4px;background:#ddd">-key-</th>';
                foreach ($row as $field => $value) {
                    echo '<th style="padding:0 4px;background:#ddd">' . $field . '</th>';
                }
                echo '</tr>';
                $first = false;
            }
            // TD
            echo '<tr>';
            echo '<td style="padding:0 4px;background:#fff">' . $key . '</td>';
            foreach ($row as $field => $value) {
                echo '<td style="padding:0 4px;background:#fff">' . $value . '</td>';
            }
            echo '</tr>';
        }
    }
    echo '</table>';
    echo '</div>';
    if ($func) {
        echo '<div class="col-lg-6">';
        echo '<pre>' . getFunctionSourceCode($func) . '</pre>';
        echo '</div>';
    }
    echo '</div>';
    echo '<br />';
}

/**
 *
 * @param callable $func
 * @return string
 */
function getFunctionSourceCode($func)
{
    $reflection = new ReflectionFunction($func);
    $filename = $reflection->getFileName();
    $start_line = $reflection->getStartLine();
    $end_line = $reflection->getEndLine() - 1;
    $length = $end_line - $start_line;

    $lines = file($filename);
    $lines = array_slice($lines, $start_line, $length);
    $lines = array_map(function ($s) {
        return substr($s, 4);
    }, $lines);
    if (count($lines) && strpos($lines[0], 'return ') === 0) {
        $lines[0] = substr($lines[0], 7);
    }
    return implode("", $lines);
}
