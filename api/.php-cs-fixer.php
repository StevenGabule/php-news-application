<?php

$finder = PhpCsFixer\Finder::create()
  ->in(__DIR__ . "/src")
  ->name('*.php')
  ->ignoreDotFiles(true)
  ->ignoreVSC(true);

return (new PhpCsFixer\Config())
  ->setRules(['@PSR12' => true])
  ->setFinder($finder);