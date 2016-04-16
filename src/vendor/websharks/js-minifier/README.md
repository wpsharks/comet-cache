## JavaScript Minification

JavaScript minifier (i.e., compressor).

[![](https://img.shields.io/github/license/websharks/js-minifier.svg)](https://github.com/websharks/js-minifier/blob/HEAD/LICENSE.txt)
[![](https://img.shields.io/badge/made-w%2F_100%25_pure_awesome_sauce-AB815F.svg?label=made)](http://websharks-inc.com/)
[![](https://img.shields.io/badge/by-WebSharks_Inc.-656598.svg?label=by)](http://www.websharks-inc.com/team/)
[![](https://img.shields.io/github/release/websharks/js-minifier.svg?label=latest)](https://github.com/websharks/js-minifier/releases)
[![](https://img.shields.io/packagist/v/websharks/js-minifier.svg?label=packagist)](https://packagist.org/packages/websharks/js-minifier)
[![](https://img.shields.io/github/issues/websharks/js-minifier.svg?label=issues)](https://github.com/websharks/js-minifier/issues)
[![](https://img.shields.io/github/forks/websharks/js-minifier.svg?label=forks)](https://github.com/websharks/js-minifier/network)
[![](https://img.shields.io/github/stars/websharks/js-minifier.svg?label=stars)](https://github.com/websharks/js-minifier/stargazers)
[![](https://img.shields.io/github/downloads/websharks/js-minifier/latest/total.svg?label=downloads)](https://github.com/websharks/js-minifier/releases)
[![](https://img.shields.io/packagist/dt/websharks/js-minifier.svg?label=packagist)](https://packagist.org/packages/websharks/js-minifier)

---

## Installation Instructions (Two Options)

1. As a [Composer](https://packagist.org/packages/websharks/js-minifier) Dependency

  ```json
  {
      "require": {
          "websharks/js-minifier": "dev-master"
      }
  }
  ```

2. Or, Download the PHAR Binary
  See: https://github.com/websharks/js-minifier/releases

---

## Usage Example

```php
$js = 'var helloWorld = function() { console.log("Hello World"); };';
$compressed_js = WebSharks\JsMinifier\Core::compress($js);
```
