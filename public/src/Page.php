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

class Page
{
    private const LIST = [
        'index' => 'Introduction',
        'demo1' => 'Conditions',
        'demo2' => 'Updating / Deleting',
        'demo3' => 'Foreign tables',
    ];

    public readonly string $current;

    public function __construct(string $filename)
    {
        $this->current = basename($filename, '.php');

        if (!isset(self::LIST[$this->current])) {
            Response::NotFound->send('This page does not exists.');
        }
    }

    /**
     * @return Generator<string,string>
     */
    public function menu(): Generator
    {
        yield from self::LIST;
    }

    private function asset(string $filename): string
    {
        return basename($filename) . '?' . filemtime($filename);
    }

    public function __invoke(Closure $closure): void
    {
        ob_start();
        $resultPage = (string) $closure(); // @phpstan-ignore cast.string
        $contentPage = ob_get_clean();

        $menu = $this->renderMenu();
        $cssSimple = $this->asset(__DIR__ . '/../simple.min.css');
        $cssCustom = $this->asset(__DIR__ . '/../style.css');

        Response::OK->send(
            <<<HTML
                <!DOCTYPE html>
                <html>
                <head>
                    <title>PFDB</title>
                    <link rel="stylesheet" href="$cssSimple">
                    <link rel="stylesheet" href="$cssCustom">
                </head>
                <body>
                <header>
                    <h1>PFDB demo</h1>
                    <nav>
                        $menu
                        <a href="https://gitlab.com/arnapou/pfdb">GitLab</a>
                    </nav>
                </header>
                <main>
                    $resultPage
                    $contentPage

                </main>
                <footer>
                    <p>
                        Created & maintained by <a href="https://arnapou.net/php/pfdb">Arnapou</a>
                    </p>
                </footer>
                <script type="text/javascript">
                    var currentPage = "$this->current";
                </script>
                </body>
                </html>

                HTML
        );
    }

    private function renderMenu(): string
    {
        $menu = '';
        foreach (self::LIST as $name => $title) {
            $menu .= "<a href='$name.php'>$title</a>";
        }

        return $menu;
    }
}
