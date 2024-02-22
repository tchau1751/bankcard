# ABF - Appointment Booking Form


## Requirements
- PHP > 5.2
- MySql server
- Apache server

## How to use/install
- checkout this repository
- create `run.ini` in `config/`
- copy the data from `example.ini` in your `run.ini` and replace it with your data
- run `php bootstrap.php` (will create database and table for you)
- point apache to your folder
- go to `localhost[:port]` to use the page

## Assumptions
- you like to create a appointment with me (the host of that form)
- its just a prototype (leck of unit testing :))
- use of Apache server, otherwise the .htaccess doesn't make much sense

## Optional further development
- make independent from Apache
- use php framework, to have a stable basement for growing
- consider to move to a other technology, like Nods.JS
- use css preprocessor (LESS | SASS)
- add option to list already booked appointments
- improve appointment availability recognition for larger project (just use data for the day(s) of request, not all)
