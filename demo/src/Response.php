<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou Weather package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

enum Response: int
{
    case OK = 200;
    case NotFound = 404;
    case InternalServerError = 500;

    public function send(string $message = ''): void
    {
        [$code, $text] = [$this->getCode(), $this->getText()];

        http_response_code($code);

        header('Content-Type: text/html; charset=UTF-8');
        if ($code >= 200 && $code < 300) {
            echo $message;
        } else {
            echo "<h1>$code $text</h1><p>" . nl2br($message) . '</p>';
            exit;
        }
    }

    public function getCode(): int
    {
        return $this->value;
    }

    public function getText(): string
    {
        return (string) preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name);
    }
}
