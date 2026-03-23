<?php

/**
 * Generate the pathload.main.php file using the composer metadata.
 *
 * The general aim is to find all the autoloading rules from the composer
 * packages and then produce equivalent autoloading rules for the
 * namespace-prefixed PHAR.
 *
 * To inspect/debug, you can run this script directly on the CLI:
 *
 * $ php generate-pathload.main.php library-name 1.2.3
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
function buildPathloadConfig(array $installed, array $scoper): array {
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

function parseArgs(array $args): array {
  $errors = [];
  $parsed = [];
  if (isset($args[1])) {
    $parsed['lib-name'] = $args[1];
  }
  else {
    $errors[] = 'Missing <LIB-NAME> argument.';
  }
  if (isset($args[2])) {
    $parsed['lib-ver'] = $args[2];
  }
  else {
    $errors[] = 'Missing <LIB-VER> argument.';
  }

  if (!empty($errors)) {
    fprintf(STDERR, "usage: %s <LIB-NAME> <LIB-VER>\n", $args[0]);
    fprintf(STDERR, implode("\n", $errors) . "\n");
    exit(1);
  }

  return $parsed;
}

function buildPhp($progArgs, $config, $scoper) {
  $namespace = $scoper['prefix'];
// echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  echo '<?' . "php\n";
  printf("namespace %s;\n", $namespace);
  printf("\pathload()->activatePackage(%s, __DIR__, %s);\n",
    var_export($progArgs['lib-name'] . '@' . explode('.', $progArgs['lib-ver'])[0], TRUE),
    var_export($config, TRUE)
  );

  $classmapFile = __DIR__ . '/vendor/composer/autoload_classmap.php';
  if (file_exists($classmapFile) && !empty(require $classmapFile)) {
    printf("function classMapLoader(\$class) {\n");
    printf("  static \$map;\n");
    printf("  \$map ??= require __DIR__ . %s;\n", var_export("/vendor/composer/autoload_classmap.php", TRUE));
    printf("  if (isset(\$map[\$class])) { require_once \$map[\$class]; }\n");
    printf("}\n");
    printf("\n");
    printf("spl_autoload_register(%s);\n", var_export('\\' . $namespace . '\\classMapLoader', TRUE));
    printf("\n");
  }
}

$progArgs = parseArgs($argv);
$scoper = require __DIR__ . '/scoper.inc.php';
$installed = json_decode(file_get_contents(
  __DIR__ . '/vendor/composer/installed.json'
), TRUE);
$config = buildPathloadConfig($installed, $scoper);
buildPhp($progArgs, $config, $scoper);
