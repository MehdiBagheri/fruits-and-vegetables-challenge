<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->name('*.php')
    ->exclude('vendor')
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
