## CSS Minification

CSS minifier (i.e., compressor).

[![](https://img.shields.io/github/license/websharks/css-minifier.svg)](https://github.com/websharks/css-minifier/blob/HEAD/LICENSE.txt)
[![](https://img.shields.io/badge/made-w%2F_100%25_pure_awesome_sauce-AB815F.svg?label=made)](http://websharks-inc.com/)
[![](https://img.shields.io/badge/by-WebSharks_Inc.-656598.svg?label=by)](http://www.websharks-inc.com/team/)
[![](https://img.shields.io/github/release/websharks/css-minifier.svg?label=latest)](https://github.com/websharks/css-minifier/releases)
[![](https://img.shields.io/packagist/v/websharks/css-minifier.svg?label=packagist)](https://packagist.org/packages/websharks/css-minifier)
[![](https://img.shields.io/github/issues/websharks/css-minifier.svg?label=issues)](https://github.com/websharks/css-minifier/issues)
[![](https://img.shields.io/github/forks/websharks/css-minifier.svg?label=forks)](https://github.com/websharks/css-minifier/network)
[![](https://img.shields.io/github/stars/websharks/css-minifier.svg?label=stars)](https://github.com/websharks/css-minifier/stargazers)
[![](https://img.shields.io/github/downloads/websharks/css-minifier/latest/total.svg?label=downloads)](https://github.com/websharks/css-minifier/releases)
[![](https://img.shields.io/packagist/dt/websharks/css-minifier.svg?label=packagist)](https://packagist.org/packages/websharks/css-minifier)

---

## Installation Instructions (Two Options)

1. As a [Composer](https://packagist.org/packages/websharks/css-minifier) Dependency

  ```json
  {
      "require": {
          "websharks/css-minifier": "dev-master"
      }
  }
  ```

2. Or, Download the PHAR Binary
  See: https://github.com/websharks/css-minifier/releases

---

## Usage Example

```php
$css = '.hello { font-family: World; }';
$compressed_css = WebSharks\CssMinifier\Core::compress($css);
```
