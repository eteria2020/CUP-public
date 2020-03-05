# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This file is in [Markdown](https://www.markdownguide.org/basic-syntax/) language.

## [1.0.61] - 2020-03-05

### Added

- Add migration for cars_bonus table

### Changed

- On CarsBonus entity, the method getUnplugEnable() manage the null value

## [1.0.60] - 2020-03-04

### Added

- Add Callback and the relative end point for Bankart payments (see ticket [871](https://sharengo.freshdesk.com/a/tickets/871) and [Asynchronous status notification](https://bankart.paymentsandbox.cloud/documentation/gateway#asynchronous-status-notification))

## [1.0.59] - 2020-03-03

### Changed

- In new-signup.phtml, add a warning message that appear if a customer select Milan/Rome fleet or try to submit

## [1.0.58] - 2020-03-03

### Changed

- Fix some bug on overlay in signup-nl1.phtml, signup-si1.phtml, signup-sk1.phtml 

## [1.0.57] - 2020-03-02

### Add

- New documentation management through views and pages are stored on database (this for FAQ, legal notes, etc.) (see [ticket 650](https://sharengo.freshdesk.com/a/tickets/650)) 

## [1.0.56] - 2020-03-02

### Changed

- fix some bug on additional services view and removed old code

## [1.0.55] - 2020-02-26

### Changed

- update pricing for Slovakia server instance
- fix problem on overlay class "sng-overlay"

## [1.0.54] - 2020-02-25

### Changed

- fix send mail on wrong payments
- update html of mail 5 ('profilo sospeso')
- fix log on pay invoice

## [1.0.53] - 2020-02-20

### Changed

- Fix Security "Sharengo web portals [WA-PT 3343]"

- Description  
  The software generates an error message that includes sensitive information about its environment, users, or associated data.

- Remediation  
  Handle exceptions internally and do not display errors containing potentially sensitive information to a user.

- Solution  
  the following lines have been changed in the "module.config.php" file
    - 'display_not_found_reason' => false,
    - 'display_exceptions' => false,

## [1.0.52] - 2020-02-19

### Added

- In additional-services.phtml add a warning message for Milan fleet

### Changed

- Changed some similar labels to optimize the translation

## [1.0.51] - 2020-02-19

### Changed

- In new-signup.phtml, add a warning message that appear if a customer select Milan fleet or try to submit

## [1.0.50] - 2020-02-18

### Changed

- changed label on new-signup2 page, for Italian drivers (B/B1 dirver license mandatory)
- restore retry payments to 60 day

## [1.0.49] - 2020-02-18

### Changed

- Improvements inside AddressController (update trip's address non payable, filter some latitude or longitude invalid)


## [1.0.48] - 2020-02-14

### Changed

- Modified AddressController for reverse geocoding via Nominatim of Open Street Map

## [1.0.47] - 2020-02-12

### Changed

- the payment retention period has been increased to 30 days instead of 60 days (ticket 494)

## [1.0.46] - 2020-02-11

### Changed

- Update Slovenian translation
- Inside public-business-module, hide the card request inside the pin page for Slovakian instance (see [ticket 781](https://sharengo.freshdesk.com/a/tickets/781))

## [1.0.45] - 2020-02-11

### Changed

- Fix Slovenian translation on first page od signup (see [ticket 767](https://sharengo.freshdesk.com/a/tickets/767))
- Update core module for block email 'bcaoo.com' (see [ticket 778](https://sharengo.freshdesk.com/a/tickets/778))

## [1.0.44] - 2020-02-07

### Changed

- In user-area for Italian version, add checkbox for optional contract conditions (news letter and general condition 1)

## [1.0.43] - 2020-02-06

### Changed

- Fix html escape char inside translations

## [1.0.42] - 2020-02-05

### Changed

- Fix some problems with "mobile" view for app in UserAreaController.php (see [ticket 683](https://sharengo.freshdesk.com/a/tickets/683))
- Add translator inside AdditionalServicesController.php
- Update po files

### Deleted

- All reference to gift-packages

## [1.0.41] - 2020-02-04

### Changed

- Update PDF privacy document for Italian version
- Update new-signup.phtml, signup-nl1.phtml, signup-si1.phtml and signup-sk1.phtml

## [1.0.40] - 2020-02-03

### Changed

- In new-signup change the checkbox form (see [ticket 662](https://sharengo.freshdesk.com/a/tickets/662))
    - privacy (mandatory), in db *customers.privacy_information*
    - vexatious clauses (mandatory), in db *customers.general_condition2*
    - new letter (optional), in db *customers.newsletter*
    - insurance info (optional), in db *customers.general_condition1*

## [1.0.39] - 2020-01-30

### Changed

- Modified rates page (see [ticket 681](https://sharengo.freshdesk.com/a/tickets/681))

## [1.0.38] - 2020-01-29

### Changed

- Modified amount of change card to 0,01 euro

## [1.0.37] - 2020-01-21

### Added 

- Add new script shell clean_mongo.sh, remove old data from Mongo collections (12 months)

## [1.0.36] - 2020-01-17

### Changed 

- Update UserController in co2Action, check customer id param via post method

## [1.0.35] - 2020-01-15

### Changed 

- Update signup-nl1.phtml, cost from 16 to 22 cent par minute
- Update all translations (file *.po)
- Removed discounterSite from local.php

## [1.0.34] - 2020-01-13

### Changed 

- Update contract for NL (see [ticket 617](https://sharengo.freshdesk.com/a/tickets/617))

## [1.0.33] - 2020-01-13

### Changed 

- Changed checkAlarmsAction, move flush inside persist branch

## [1.0.32] - 2020-01-10

### Changed

- Changed clean_commands.sh, for customer_logs table, shorted the retention period from 10 months to 8 month
- Changed checkAlarmsAction, put the flush() inside the loop

## [1.0.31] - 2020-01-09

### Changed

- Modify the warning message in user area, for customers driver license invalid or checking process 

## [1.0.30] - 2020-01-07

### Changed

- Change the subscription message's amount from 5 € to 10 € for Holland instance 
- Fix some translation in user area

## [1.0.29] - 2020-01-03

### Changed

- Added some images to send mails 

## [1.0.28] - 2019-12-19

### Changed

- Update module Gpwebpay

## [1.0.27] - 2019-12-18

### Changed

- Various labels translated into User Area

## [1.0.26] - 2019-12-18

### Changed

- Remove the link "maggiori info" from dati-pagamento.phtml of server instance not Italian

## [1.0.25] - 2019-12-17

### Changed

- Disabled re-new of discounts and enabled the disable old discounts

## [1.0.24] - 2019-12-16

### Changed

- Fix translation in signup-nl2.phtml
- Update all file *.po

## [1.0.23] - 2019-12-16

### Changed

- Add a translation inside RegistrationService.notifySharengoByMail()
- Update all file *.po

## [1.0.22] - 2019-12-13

### Changed

- In NewUserFieldset.php, remove the fleet of Modena from fleets array
- In new-signup.phtml and login.phtm, add 'autocomplete' attribute for email and password field

## [1.0.21] - 2019-12-13

### Added

- To send the forgot password' email, with the link with the right translation, we add the follow files: 
    - file forgot-password-nl_NL.phtml
    - file forgot-password-sk_SL.phtml
    - file forgot-password-sl_SI.phtml

Note: remember to update file config/autoload/goalioforgotpassword.local.php

## [1.0.20] - 2019-12-12

### Changed

- Module SharengoCore
- Module Bankart
- Fix bug inside subscription for country
- Update translations

## [1.0.19] - 2019-12-10

### Changed

- Fix some problem on the page Trip inside  user area


## [1.0.18] - 2019-12-10

### Added

- New module in vendor/sharengo-web/bankart (see documentation [Bankart](https://gateway.bankart.si/documentation/gateway) )

### Changed

- Module SharengoCore for new payment gateway

## [1.0.17] - 2019-12-09

### Changed

- Fix the translation of strings in the footer (ticket 397)

## [1.0.16] - 2019-12-04

### Changed

- Fix Signup (ticket 377)
- Remove files about Intercom and criteo 

## [1.0.15] - 2019-12-03

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

- fix left menu user area (ticket 306)


## [1.0.11] - 2019-11-27

### Changed

- update SahrengoCore module
- update some translation in Slovenian language

## [1.0.10] - 2019-11-27

### Changed

- fix getListZonesAction in IndexController

## [1.0.9] - 2019-11-26

### Added

- add migration file for penalties table 201911261620_alter_penalties_logistic.sql

## [1.0.8] - 2019-11-15

### Changed

- change legal conditions in sigup2 for Slovenia (ticket 244)
- change gender translation in sigup2 for Slovenia (ticket 253)

## [1.0.7] - 2019-11-13

### Changed

- fix coocky translation for Slovenian language (ticket 112)
- update the contract (ticket 222)
- update link https://site.sharengo.si/kako-deluje-sharengo/ (ticket 223)
- update the label PAKETI IN PROMOCIJE (ticket 226)
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
- map.phtm remove commented ref to carsUrl
- signup changed (see ticket 171)


## [1.0.0] - 2019-10-31 

### Added

- Flush() inside checkAlarmsAction()
- Dettaglio_tariffe.pdf
- CHANGELOG.md and CHANGELOG.md

### Changed

- Slovenian traslations