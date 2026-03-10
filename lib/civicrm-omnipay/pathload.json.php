<?php

/**
 * Generate the pathload.json file using the composer metadta (installed.json).
 *
 * The general aim is to find all of the autoloading rules from the composer
 * packages and then produce equivalent autoloading rules for the
 * namespace-prefixed PHAR.
 *
 * To inspect/debug, you can run this script directly on the CLI (`php pathload.json.php`).
 */

/**
 * Normalize the path-delimiter.
 */
function normalizePath(string $path): string {
  return str_replace(DIRECTORY_SEPARATOR, '/', $path);
}

/**
 * Given the 'installed-path' of package (relative to vendor/composer/), determine the
 * final "archive" path.
 */
function toArchivePath(string $expression): string {
  $baseDir = normalizePath(realpath(__DIR__));
  $absoluteTarget = normalizePath(realpath("$baseDir/vendor/composer/" . $expression));
  if (str_starts_with($absoluteTarget, "$baseDir/")) {
    return substr($absoluteTarget, strlen("$baseDir/"));
  }
  else {
    throw new \Exception("Failed to determine archive path for \"$expression\".");
  }
}

/**
 * @param array $installed
 *   List of installed packages (per 'vendor/composer/installed.json')
 * @param array $scoper
 *   List of prefixing rules (per 'scoper.inc.php').
 * @return array
 *   The autoloading rules for this library.
 * @see https://github.com/totten/pathload-poc/
 */
function buildPathloadJson(array $installed, array $scoper): array {
  $privateNamespace = $scoper['prefix'] . '\\';
  $config = [];
  foreach ($installed['packages'] ?? [] as $package) {
    if (empty($package['install-path'])) {
      // Replace used.
      continue;
    }
    $installPath = toArchivePath($package['install-path']);

    foreach (['include', 'psr-4', 'psr-0'] as $section) {
      foreach ($package['autoload'][$section] ?? [] as $nsPrefix => $nsPaths) {
        foreach ((array) $nsPaths as $nsPath) {
          $config['autoload'][$section][$privateNamespace . $nsPrefix] ??= [];
          $config['autoload'][$section][$privateNamespace . $nsPrefix][] = $installPath . '/' . $nsPath;
        }
      }
    }
  }

  return $config;
}

$scoper = require __DIR__ . '/scoper.inc.php';
$installed = json_decode(file_get_contents(
  __DIR__ . '/vendor/composer/installed.json'
), TRUE);

$config = buildPathloadJson($installed, $scoper);
echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo "\n";
