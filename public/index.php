<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\RedirectResponse;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    
    // 检查当前路径是否为根路径
    if ($_SERVER['REQUEST_URI'] === '/') {
        return new RedirectResponse('/login');
    }
    
    return $kernel;
};
