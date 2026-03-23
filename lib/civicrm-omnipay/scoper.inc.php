<?php declare(strict_types = 1);

$scopeFilters = new class() {

  /**
   * Convert "Omnipay\Foo\Bar" to CiviOmniPay\Omnipay\Foo\Bar::CLASS
   *
   * @param string $originalNamespace
   * @param string $prefix
   * @param string $content
   * @return array|string|string[]|null
   */
  public function preferClassConstant(string $originalNamespace, string $prefix, string &$content) {
    $content = preg_replace_callback(';\'(\\\*)' . $originalNamespace .'(\\\+[^\']+)\';', function ($matches) use ($prefix, $originalNamespace) {
      return $matches[1] . $prefix . '\\' . $originalNamespace . $matches[2] . '::CLASS';
    }, $content);
  }

  /**
   * Convert '\Omnipay\\' to '\CiviOmniPay\\Omnipay\\'
   * @param string $originalNamespace
   * @param string $prefix
   * @param string $content
   * @return void
   */
  public function replaceFragmentaryNamespace(string $originalNamespace, string $prefix, string &$content) {
    $content = preg_replace_callback(';\'(\\\*)'. $originalNamespace . '(\\\*);', function ($matches) use ($prefix, $originalNamespace) {
      return "'" . $matches[1] . $prefix . '\\' . $originalNamespace . $matches[2];
    }, $content);
  }

};

/**
 * @link https://github.com/humbug/php-scoper/blob/main/docs/configuration.md
 */
return [
  'prefix' => 'CiviOmniPay',
  'exclude-namespaces' => ['Psr\\Log'],
  'exclude-functions' => ['pathload'],
  'patchers' => [
    static function (string $filePath, string $prefix, string $content) use ($scopeFilters): string {
      $filePath = str_replace(DIRECTORY_SEPARATOR, '/', $filePath);

      if ($filePath === 'vendor/omnipay/common/src/Common/Helper.php') {
        $scopeFilters->replaceFragmentaryNamespace('Omnipay', $prefix, $content);
      }

      if ($filePath === 'vendor/omnipay/paypal/src/RestGateway.php') {
        $scopeFilters->preferClassConstant('Omnipay', $prefix, $content);
      }
      return $content;
    },
  ],
];
