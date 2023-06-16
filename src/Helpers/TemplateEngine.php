<?php

declare(strict_types=1);

namespace Challenge\Helpers;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

/**
 * A wrapper to the template engine to set it up and use it where needed.
 */
class TemplateEngine
{
    private Mustache_Engine $mustache;

    public function __construct(string $baseDirectory)
    {
        $this->mustache = new Mustache_Engine([
            'cache' => get_temp_dir() . 'mustache',
            'loader' => new Mustache_Loader_FilesystemLoader($baseDirectory . '/templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(
                $baseDirectory . '/templates/partials'
            ),
        ]);
    }

    public function render(string $templateName, array $data): string
    {
        $template = $this->mustache->loadTemplate($templateName);
        return $template->render($data);
    }
}
