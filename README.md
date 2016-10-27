# NeoFusionJsonRpcBundle

JSON-RPC 2.0 Server for Symfony

[![Build Status](https://travis-ci.org/NeoFusion/JsonRpcBundle.svg?branch=master)](https://travis-ci.org/NeoFusion/JsonRpcBundle)

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require neofusion/json-rpc-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new NeoFusion\JsonRpcBundle\NeoFusionJsonRpcBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Configure API methods

You can easily define methods in your configuration file:

```yaml
neofusion_jsonrpc:
    routing:
        customer:
            path: /customer
            methods:
                comment.create: { service: 'app.api.customer.comment', action: 'create' }
                comment.delete: { service: 'app.api.customer.comment', action: 'delete' }
```

* `routing` - list of routes
* `customer` - internal name of a route
* `path` - second part of URL after prefix
* `methods` - list of methods
* `comment.create` - method name
* `service` - name of a service, which contains callable methods
* `action` - callable method from the service

### Step 4: Register the routes

Finally, register this bundle's routes by adding the following to your project's routing file:

```yaml
# app/config/routing.yml
neofusion_jsonrpc:
    resource: "@NeoFusionJsonRpcBundle/Resources/config/routing.yml"
    prefix: /api
```
