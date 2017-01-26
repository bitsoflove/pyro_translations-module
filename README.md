# pyro_translations-module
Generic streams translations module for PyroCMS 3.1 and up
(using [react-handsontable](https://github.com/handsontable/react-handsontable))

## Installation

- `composer require bitsoflove/pyro_translations-module`
- `php artisan module:install translations`

## Configuration

> Todo: figure out how to publish module config

> Todo: example configurations

## Developing
Instead of installing via composer, create the folder `core/bitsoflove`, then clone this repo into `core/bitsoflove/translations-module` 
**(make sure you remove the `pyro_` prefix)**

- `cd core/bitsoflove/translations-module`
- `cp .env.example.js .env.js`
- Change the [proxy](https://github.com/bitsoflove/pyro_translations-module/blob/master/.env.example.js#L4) property in `.env.js` to the host of your local pyro application
- `npm install`
- `npm run dev`
```
