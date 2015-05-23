#LosBase
[![Build Status](https://travis-ci.org/Lansoweb/LosBase.svg?branch=master)](https://travis-ci.org/Lansoweb/LosBase) [![Latest Stable Version](https://poser.pugx.org/los/losbase/v/stable.svg)](https://packagist.org/packages/los/losbase) [![Total Downloads](https://poser.pugx.org/los/losbase/downloads.svg)](https://packagist.org/packages/los/losbase) [![Coverage Status](https://coveralls.io/repos/Lansoweb/LosBase/badge.svg)](https://coveralls.io/r/Lansoweb/LosBase) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Lansoweb/LosBase/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Lansoweb/LosBase/?branch=master) [![SensioLabs Insight](https://img.shields.io/sensiolabs/i/72de3f91-4d5b-4d34-a653-197975ce4c17.svg?style=flat)](https://insight.sensiolabs.com/projects/72de3f91-4d5b-4d34-a653-197975ce4c17) [![Dependency Status](https://www.versioneye.com/user/projects/54da829bc1bbbd5f820002d2/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54da829bc1bbbd5f820002d2)

## Introduction
This module provides some utility classes for ZF2 projects and other [LOS modules](http://leandrosilva.info/modulos-zf2)

## Requirements
- PHP 5.4 or greater
- Zend Framework 2 [framework.zend.com](http://framework.zend.com/).

## Instalation
Instalation can be done with composer ou manually

### Installation with composer
For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).

  1. Enter your project directory
  2. Create or edit your `composer.json` file with following contents:

     ```json
     {
         "minimum-stability": "dev",
         "require": {
             "los/losbase": "~2.5"
         }
     }
     ```
  3. Run `php composer.phar install`
  4. Open `my/project/directory/config/application.config.php` and add `LosBase` to your `modules`
     
### Installation without composer

  1. Clone this module [LosBase](http://github.com/LansoWeb/LosBase) to your vendor directory
  2. Enable it in your config/application.config.php like the step 4 in the previous section.
  
## Usage

### CRUD

The module provides a console interface for easily creating a CRUD module:
```
php public/index.php create crud <modulename>
```

And it will create all necessary files and directories (config, controller, entity and service)

### Controller

The AbstractCrudController provides some common operations for simples a CRUD:
* list
* view
* add
* edit
* delete

### Doctrine types
* UtcDateTime: converts the datetime to UTC before saving to the database
* BrDateTime: converts the datetime to UTC before saving to the database and to BRST (UTC-3) when loading from database
* BrPrice: handles brazillian price format (1.234,56) for databae operations

### Doctrine Entities
* 3 Traits: Id, Created and Updated
* AbstractEntity already using the 3 basic traits above

### Module
* AbstractModule providing getAutoloaderConfig and getConfig basic methods

### Doctrine Entity Service
* AbstractEntity provides and abstract service class that handles saves and deletes for doctirne entities
* Util: getUserAgent and getIP
* Uuid: static method for UUID creation

### Doctirne validators
* NoEntityExists asserts that no entity with the specified field already exists during add operation
* NoOtherEntityExists asserts that no other entity with the specified field already exists during edit operation

