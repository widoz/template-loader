[![Build Status](https://travis-ci.org/widoz/template-loader.svg?branch=master)](https://travis-ci.org/widoz/template-loader)
[![codecov](https://codecov.io/gh/widoz/template-loader/branch/master/graph/badge.svg)](https://codecov.io/gh/widoz/template-loader)

# WordPress Template Loader

A simple hookable template loader for WordPress. Allow you to load templates in chain from child to plugin.

This is not for the WordPress templates in the form of "name-slug.php" with fallback to name.php.
Indeed this is a loader to build a basic data injection for views (templates).

## Requirements
Php >= 5.6.x

## Examples

The `TemplateLoader\Loader` class make use of the `Fluent` interface, so it's possible to concatenate
the calls to ask the instance to do the things.

```php
$loader = new TemplateLoader\Loader('template_slug', new TemplateLoader\DataStorage());

$loader->withData(new DataInterface())
       ->usingTemplate('/relative/file/path.php')
       ->render()
```

The class make use of WordPress function `locate_template` to locate the template file within the child and parent theme.

If you use the library within a plugin it's possible to define a fallback template part file path.
As the name *fallback* says the file will be loaded only in case nothing is found into the previous locations.

```php
$loader = new TemplateLoader\Loader('template_slug', new TemplateLoader\DataStorage());

$loader->withData(new DataInterface())
       ->usingTemplate('/relative/file/path.php')
       ->butFallbackToTemplate('/plugin/relative/file/path.php')
       ->render();
```

## Data Type

The data type used to inject values into the template is a class named `TemplateLoader\DataInterface`.
`DataInterface` doesn't declare any method. It's just a way to ensure the correct type of data is passed into the template loader.

This way we can extends the interface to create our own contracts based on the specific view.

## Hooks

The `render` method, perform some filters that allow third party code to hook into the data and template
to be modified before the template file is loaded.

There are two filter: `tmploader_template_engine_data` that is generic and pass the `$data` value and the `slug` property.

```php
add_filter('tmploader_template_engine_data', function(TemplateLoader\DataInterface $data, string $slug) {

    switch($slug) {
        case 'my_slug':
            // Do something
        break;

        default:
        break;
    }

    return $data;

});
```

The second one is similar but the filter name include the slug template: `"tmploader_template_engine_data_{$this->slug}"`.
This in case you don't want or need to write conditional statements to know which is the current processing template.

```php
add_filter('tmploader_template_engine_data_my_template', function(TemplateLoader\DataInterface $data) {
    return new TemplateLoader\DataInterface();
});
```

## Performances

Since it's usually to call the same view in different portion of the same page, so whitin the same
http request, to prevent to access multiple time to the file system only to know where the file template
is located, we defined an internal collection that can be used to store the template file paths.

The second time we try to ask the same template, we'll not perform any additional filesystem access in order
to load the template file. This improve speed for multiple calls. Also since we don't create a strict relation
betwee the template and the data to inject, we can pass every time a different data value.

So, just to clarify with an example, during the same call we can instantiate the loader once and than ask to
load the same template multiple times with different data values.

```php
$loader = new TemplateLoader\Loader('template_slug', new TemplateLoader\DataStorage());

$loader->withData(new DataInterface())
       ->usingTemplate('/relative/file/path.php')
       ->butFallbackToTemplate('/plugin/relative/file/path.php')
       ->render();

// Some code ... and then ...

$loader->withData(new DataInterface())
       ->render();
```

The second time we call the `render` method we had changed only the data used within the template since
we have stored the template path related with the `slug` of the template. In this case `template_slug`.

Avoiding unnecessary filesystem access that we know are slows.