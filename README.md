# WP Endpoints: Admin Bar

> Generic endpoint to expose admin bar configuration via WP-API. This extension will create an endpoint (at ```/wp-json/leean/v1/admin-bar``` by default).

The endpoint takes a no parameters and returns the following data:

- Site Name
- Current User
- Admin URL
- Logout URL
- Edit Page URL
- New Items
   - New Item Name
   - New Item URL


## Getting Started

The easiest way to install this package is by using composer from your terminal:

```bash
composer require moxie-lean/wp-endpoints-admin-bar
```

Or by adding the following lines on your `composer.json` file

```json
"require": {
  "moxie-lean/wp-endpoints-admin-bar": "dev-master"
}
```

This will download the files from the [packagist site](https://packagist.org/packages/moxie-lean/wp-endpoints-admin-bar) 
and set you up with the latest version located on master branch of the repository. 

After that you can include the `autoload.php` file in order to
be able to autoload the class during the object creation.

```php
include '/vendor/autoload.php';
```

Finally you need to initialise the endpoint by adding this to your code:

```php
\Lean\Endpoints\AdminBarApi::init();
```

## Usage

The extension has a number of filters which can be used to customised the output. In addition it does some useful extra manipulation of ACF data to make it more useful to a front-end app.

### Filters

Common parameters passed by many filers are:

- $endpoint : the name of the endpoint. Always '/admin-bar' for this extension.

#### ln_endpoints_api_namespace
Customise the API namespace ('leean' in ```/wp-json/leean/v1/admin-bar```)

```php
add_filter( 'ln_endpoints_api_namespace', function( $namespace, $endpoint ) {
    return 'my-app';
}, 10, 2 );
```

#### ln_endpoints_api_version

Customise the API version ('v1' in ```/wp-json/leean/v1/admin-bar```)

```php
add_filter( 'ln_endpoints_api_version', function( $version, $endpoint ) {
    return 'v2';
}, 10, 2 );
```

#### ln_endpoints_data  

Customise the results just before they are sent.

```php
add_filter( 'ln_endpoints_data', function( $data, $endpoint ) {
    $data['site_name'] = 'New Site Name';
    return $data;
}, 10, 3 );
```
