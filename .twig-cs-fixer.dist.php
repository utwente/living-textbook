<?php

return (new TwigCsFixer\Config\Config())
  ->allowNonFixableRules()
  ->setRuleset(
    (new TwigCsFixer\Ruleset\Ruleset())
      ->addStandard(new TwigCsFixer\Standard\Symfony())
      ->overrideRule(new TwigCsFixer\Rules\File\DirectoryNameRule(baseDirectory: 'templates', ignoredSubDirectories: ['bundles', 'components'], optionalPrefix: '_')) // Adapt to allow underscore as prefix
      ->addStandard(new TwigCsFixer\Standard\TwigCsFixer())
  )
  ->setFinder(
    TwigCsFixer\File\Finder::create()
      ->in(__DIR__ . DIRECTORY_SEPARATOR . 'templates')
  );
