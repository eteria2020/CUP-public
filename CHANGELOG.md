# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This file is in [Markdown](https://www.markdownguide.org/basic-syntax/) language.

## [1.0.19] - 2019-12-13

### added

- To send the forgot password' email, with the link with the right translation, we add the follow files: 
    - file forgot-password-nl_NL.phtml
    - file forgot-password-sk_SL.phtml
    - file forgot-password-sl_SI.phtml

Note: remember to update file config/autoload/goalioforgotpassword.local.php

## [1.0.18] - 2019-12-12

### Changed

- Module SharengoCore
- Module Bankart
- Fix bug inside subscription for country
- Update translations

## [1.0.17] - 2019-12-10

### Changed

- Fix some problem on the page Trip inside  user area


## [1.0.16] - 2019-12-10

### Added

- New module in vendor/sharengo-web/bankart (see documentation [Bankart](https://gateway.bankart.si/documentation/gateway) )

### Changed

- Module SharengoCore for new payment gateway

## [1.0.15] - 2019-12-09

### Changed

- Fix the translation of strings in the footer (ticket 397)

## [1.0.14] - 2019-12-04

### Changed

- Fix Signup (ticket 377)
- Remove files about Intercom and criteo 

## [1.0.13] - 2019-12-03

### Changed

- clean libraries smsgatewayme, criteo and Intercom (ticket 349)

## [1.0.14] - 2019-12-02

### Changed

- Split the 'dati-pagamento' view for each country
- Update Mollie module

## [1.0.13] - 2019-11-28

### Changed

- The file 201911261620_alter_penalties_logistic.sql has been added to make a change to the penalties table of the db. 2 fields have been added: logistic_description and logistic.


## [1.0.12] - 2019-11-27

### Changed

- fix left menu userarea (ticket 306)


## [1.0.11] - 2019-11-27

### Changed

- update SahrengoCore module
- update some translation in Slovenian language

## [1.0.10] - 2019-11-27

### Changed

- fix getListZonesAction in IndexController

## [1.0.9] - 2019-11-26

### Added

- add migration file for penalities table 201911261620_alter_penalties_logistic.sql

## [1.0.8] - 2019-11-15

### Changed

- change legal conditions in sigup2 for Slovenia (ticket 244)
- change gender traslation in sigup2 for Slovenia (ticket 253)

## [1.0.7] - 2019-11-13

### Changed

- fix coocky traslation for Slovenian language (ticket 112)
- update the contract (ticket 222)
- update link https://site.sharengo.si/kako-deluje-sharengo/ (ticket 223)
- update the lableb PAKETI IN PROMOCIJE (ticket 226)
- language menu show only two languages (the instance and English language)

## [1.0.6] - 2019-11-12

### Changed

- add VAT code inside sign-up Slovenia (ticket 209)
- fix some translation (ticket 218)

## [1.0.5] - 2019-11-11

### Changed

- replace http with https when call Nominatim
- fix a record in Slovenian translation

## [1.0.4] - 2019-11-08

### Changed

- fix redirect to signup-3 for Slovenia inside UserController

## [1.0.3] - 2019-11-08

### Added

- add new validator TaxCodeSi inside SharengoCore for Slovenia ((see ticket 172)
- fix some translation on signup-si2

## [1.0.2] - 2019-11-08

### Changed

- Slovenian traslation of sign-up phase 2 (see ticket 172, only translations)

## [1.0.1] - 2019-11-06

### Changed

- Slovenian traslation
- Link of header solenia
- criteo.js remove personal email
- map.phtm remove comented ref to carsUrl
- signup changed (see ticket 171)


## [1.0.0] - 2019-10-31 

### Added

- Flush() inside checkAlarmsAction()
- Dettaglio_tariffe.pdf
- CHANGELOG.md and CHANGELOG.md

### Changed

- Slovenian traslations