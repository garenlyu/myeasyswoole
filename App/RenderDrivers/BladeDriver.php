<?php

namespace App\RenderDrivers;

use Jenssegers\Blade\Blade;
use EasySwoole\Template\RenderInterface;

class BladeDriver implements RenderInterface
{
    private $template;

    public function __construct()
    {
        $this->template = new Blade(EASYSWOOLE_ROOT . '/App/Views', EASYSWOOLE_ROOT . '/Caches/Views');

    }

    public function render(string $template, array $data = null, array $options = null): ?string
    {
        // TODO: Implement render() method.
        return $this->template->render($template,$data);
    }

    public function afterRender(?string $result, string $template, array $data = [], array $options = [])
    {
        // TODO: Implement afterRender() method.
    }

    public function onException(\Throwable $throwable, $arg): string
    {
        // TODO: Implement onException() method.
        return $throwable->getMessage();
    }
}