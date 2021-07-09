# Core —— Goodcatch Laravel Modules extension

required basic module goodcatch/laravel-modules

## Introduction

Core module include many functions that helps development more effective.

## Installation

Just copy the entire project to path **app/Modules** or pre-configured path that defined value of '**MODULE_INSTALL_PATH**' in environment file;

Make sure folder name is exactly 'Core'.

### config module core

#### db migration

```shell script

php artisan module:migrate core


```

#### seed

[regions](https://github.com/wecatch/china_regions/releases)


```shell script

php artisan module:seed core


```

#### queue

```shell script

php artisan queue:work redis --queue=high,default,emails,low --sleep=3 --tries=3 --delay=2 --timeout=600


```

#### schedule
```shell script
php artisan schedule:run >> /dev/null 2>&1

```

### Lightcms part

#### front end dependency

[Layui update](https://res.layui.com/static/download/layui/layui-v2.5.7.zip?v=1)

[xm-select](https://gitee.com/maplemei/xm-select)


