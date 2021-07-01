<?php

namespace App\RenderDriver;

use duncan3dc\Laravel\BladeInstance;
use EasySwoole\Template\RenderInterface;

class Blade implements RenderInterface
{
    private $template;

    public function __construct()
    {
        $this->template = new BladeInstance(EASYSWOOLE_ROOT . '/App/Views', EASYSWOOLE_ROOT . '/App/Caches/Views');

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