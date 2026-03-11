<?php declare(strict_types = 1);

/**
 * @link https://github.com/humbug/php-scoper/blob/main/docs/configuration.md
 */
return [
  'prefix' => 'CiviOmniPay',
  'exclude-namespaces' => ['Psr\\Log'],
  'exclude-functions' => ['pathload'],
  'patchers' => [
    static function (string $filePath, string $prefix, string $content): string {
      $filePath = str_replace(DIRECTORY_SEPARATOR, '/', $filePath);
      if ($filePath === 'vendor/omnipay/common/src/Common/Helper.php') {
        // Convert '\Omnipay\\' to '\CiviOmniPay\\Omnipay\\'
        $content = preg_replace_callback(';\'(\\\*)Omnipay(\\\*)\';', function ($matches) use ($prefix) {
          return "'" . $matches[1] . $prefix . '\\\\Omnipay' . $matches[2] . "'";
        }, $content);
      }
      return $content;
    },
  ],
];
