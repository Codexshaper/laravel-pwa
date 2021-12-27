[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)
[![Build Status](https://travis-ci.org/Codexshaper/laravel-pwa.svg?branch=master)](https://travis-ci.org/Codexshaper/laravel-pwa)
[![StyleCI](https://github.styleci.io/repos/279073965/shield?branch=master)](https://github.styleci.io/repos/279073965?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/Codexshaper/laravel-pwa.svg?style=flat-square)](https://scrutinizer-ci.com/g/Codexshaper/laravel-pwa)
[![Downloads](https://poser.pugx.org/Codexshaper/laravel-pwa/d/total.svg)](https://packagist.org/packages/Codexshaper/laravel-pwa)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/Codexshaper/laravel-pwa.svg?style=flat-square)](https://packagist.org/packages/Codexshaper/laravel-pwa)

# Description
Installable PWA for laravel. Implement PWA in your laravel website within 5 mins.

| Lravel PWA version      | Laravel version   |
| ---                     | ---               |
| 1.0                     | ^5.6, ^6.0, ^7.0  |
| 1.1                     | ^8.0              |
| 1.2                     | ^8.0              |

## Requirements
It only suppoorts HTTPS and localhost (both HTTP and HTTPS)

#### Click here to see video instruction.

[![IMAGE ALT TEXT HERE](https://img.youtube.com/vi/kDcy5cFH670/0.jpg)](https://www.youtube.com/watch?v=kDcy5cFH670)

## Download
```
composer require codexshaper/laravel-pwa
```

## Install

```
php artisan pwa:install
```

## Use: Add below code before closing head tag

```
{{ pwa_meta() }}
```

OR

```
@PWA
```

## Finaly configure your own information. Go to {{url}}/pwa

#### Additionaly you may add below script after all js loaded to work perfectly bootstrap 4 custom file input

```
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
```

## Contributors

* **Md Abu Ahsan Basir** - *Creator and Maintainer* - [github](https://github.com/maab16)

## Concept from [silviolleite](https://github.com/silviolleite/laravel-pwa)
