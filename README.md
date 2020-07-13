[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)
[![Build Status](https://travis-ci.org/Codexshaper/laravel-pwa.svg?branch=master)](https://travis-ci.org/Codexshaper/laravel-pwa)
[![StyleCI](https://github.styleci.io/repos/279073965/shield?branch=master)](https://github.styleci.io/repos/279073965?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/Codexshaper/laravel-pwa.svg?style=flat-square)](https://scrutinizer-ci.com/g/Codexshaper/laravel-pwa)
[![Downloads](https://poser.pugx.org/Codexshaper/laravel-pwa/d/total.svg)](https://packagist.org/packages/Codexshaper/laravel-pwa)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/Codexshaper/laravel-pwa.svg?style=flat-square)](https://packagist.org/packages/Codexshaper/laravel-pwa)

# Description
Installable PWA for laravel. Implement PWA in your laravel website within 5 mins.

## Requirements
It only suppoorts HTTPS and localhost (both HTTP and HTTPS)

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

## Contributors

* **Md Abu Ahsan Basir** - *Creator and Maintainer* - [github](https://github.com/maab16)
