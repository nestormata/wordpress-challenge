# PHP Challenge
![Build Status](https://github.com/nestormata/wordpress-challenge/actions/workflows/php.yml/badge.svg)


## Installation

The installation should be fairly easy with composer.  
No need to compile anything; the assets do not require any compiler like WebPack or any other.  
In order to install with composer you may need to specify the private repository in composer.json
like this:  

```
{
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:nestormata/wordpress-challenge.git"
        }
    ]
}
```
You will probably need to set up a token or other type of permissions, and then just run composer require on your WordPress site, like this:

```
composer require nestormata/wordpress-challenge:dev-main
```
And proceed to enable the plugin in WordPress.

## Compatibility

The plugin was tested with PHP versions 8.0, 8.1 and 8.3 and it's working, but, the phpcs libraries have compatiblity issues with PHP equal or greater than 8.1.
Several rules will fails because in 8.1 the `trim()` function deprecates sending a null parameter and that causes the rules to fail.
Running in PHP 8.0 fixes the PHPCS issue.

## Usage

Make sure the plugin is activated and the pretty URL's are enabled and working in your WordPress site.  
In order to use the plugin go the page `/users/` (the default) in your WordPress site (or the URL slug that you choosed in the options page.)  
For example:

```
http://localhost/users/
```

## Test and code check

The tests and code check are automatically ran by github.  
But, you can also run them with:

```
composer test
```
and

```
composer check
```


## Customizations by the user

### URL slug

The user is able to change on which slug to use for the URL on which it loads.  
By default the page will load under `/users/`.  
This can be configured in the admin area under Tools -> Challenge.  

### Template

The page template can be overriden in the site template by create a file called `challenge-users.php`.  
In order to use a customized template, make sure that the template includes a tag with an ID of `challenge-app`.  
Example:

```
<div id="challenge-app"></div>
```

## Cache

I've cached the AJAX requests for 1 hour using Transient cache.

## Libraries desitions

### Mustache

I'm usually more inclined to Twig for a site, but since this is just a plugin and in benefit to keep things small I decided to go with Mustache since it has no additional requirements, contrary to Twig, and still meets my requirement to serve as a template engine that allow me to keep things clean in templates.

### Guzzle

Guzzle is a pretty mature and well made HTTP client library, which complies with all the standards and it's also easy to use and easy to mock.

### Preact

For a reactive front end I would normally go with React or Vue.js, but, considering the size of the requirement and to avoid possible compatibility isues with versions of Node in the destination's environment, I decided to go with Preact using no build tools.
