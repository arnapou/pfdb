<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arnapou\PFDB\Core\TableInterface;
use Arnapou\PFDB\Query\Helper\ExprHelper;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/Page.php';
require __DIR__ . '/Parsedown.php';
require __DIR__ . '/Response.php';

function expr(): ExprHelper
{
    return new ExprHelper();
}

function showTable(string $title, TableInterface|Closure $table): void
{
    $rows = $table instanceof TableInterface
        ? $table
        : $table();

    echo '<h4>' . $title . '</h4>';
    echo '<div class="example">';
    echo '<div class="table">';
    echo "\n<table>\n";
    $first = true;
    foreach ($rows as $key => $row) { // @phpstan-ignore foreach.nonIterable
        uksort($row, 'sortkeys'); // @phpstan-ignore argument.type
        // TH
        if ($first) {
            echo '<tr>';
            echo '<th>:key</th>';
            foreach ($row as $field => $value) {
                echo '<th>' . $field . '</th>';
            }
            echo "</tr>\n";
            $first = false;
        }
        // TD
        echo '<tr>';
        echo '<td>' . $key . '</td>';  // @phpstan-ignore binaryOp.invalid
        foreach ($row as $field => $value) {
            echo '<td>' . $value . '</td>';  // @phpstan-ignore binaryOp.invalid
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo '</div>';
    echo '<div class="code">';
    if ($table instanceof Closure) {
        echo '<pre>' . getFunctionSourceCode($table) . '</pre>';
    }
    echo '</div>';
    echo '</div>';
}

function sortkeys(mixed $a, mixed $b): int
{
    return ('id' === $a ? -1 : null)
        ?? ('id' === $b ? 1 : null)
        ?? ($a <=> $b);
}

function getFunctionSourceCode(Closure $func): string
{
    $reflection = new ReflectionFunction($func);
    $filename = $reflection->getFileName();
    if (false === $filename || '' === $filename) {
        return '';
    }
    $start = (int) $reflection->getStartLine() - 1;
    $end = (int) $reflection->getEndLine() - 1;

    $lines = file($filename);
    $lines = \array_slice(\is_array($lines) ? $lines : [], $start, $end - $start);

    $firstLine = (string) preg_replace('!(static)\s+fn\s.*?=>\s*!', '', $lines[0] ?? '');
    $indent = \strlen($firstLine) - \strlen(ltrim($firstLine));

    $lines = array_merge([$firstLine], \array_slice($lines, 1));
    $lines = array_map(
        static fn (string $line): string => substr($line, $indent),
        $lines,
    );

    return implode('', $lines);
}
