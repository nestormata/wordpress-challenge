# PHP Challenge

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
            "url":  "git@bitbucket.org:nestormata/wordpress-challenge.git"
        }
    ]
}
```
And then just run composer require on your WordPress site, like this:

```
composer require nestormata/wordpress-challenge:dev-main
```
And proceed to enable the plugin in WordPress.

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
