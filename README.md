# PHP Challenge

## Customizations by the user

### URL slug
The user is able to change on which slug to use for the URL on which it loads.
By default the page will load under `/users/`.
This can be configured in the admin area under Tools -> Challenge.

### Template
The page template can be overriden in the site template by create a file called `challenge-users.php`.

## Libraries desitions

### Mustache
I'm usually more inclined to Twig for a site, but since this is just a plugin and in benefit to keep things small I decided to go with Mustache since it has no additional requirements, contrary to Twig, and still meets my requirement to serve as a template engine that allow me to keep things clean in templates.