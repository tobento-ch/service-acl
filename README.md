# ACL Service

The ACL Service is a simple role and user-level access control system.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
	- [Simple Example](#simple-example)
- [Documentation](#documentation)
	- [Acl Interface](#acl-interface)
- [Credits](#credits)
___

# Getting started

Add the latest version of the Acl service running this command.

```
composer require tobento/acl
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

## Simple Example

Here is a simple example of how to use the Acl service.

```php
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Authorizable;
use Tobento\Service\Acl\AuthorizableAware;
use Tobento\Service\Acl\Role;

// User class example.
class User implements Authorizable
{
    use AuthorizableAware;
    
    public function __construct(
        protected string $name,
    ) {}
}

// Create Acl.
$acl = new Acl();

// Adding rules.
$acl->rule('articles.read')
    ->title('Article Read')
    ->description('If a user can read articles');
    
$acl->rule('articles.create');
$acl->rule('articles.update');

// Create role.
$guestRole = new Role('guest');

// Adding permissions on role.
$guestRole->addPermissions(['articles.read']);

// Create and set user role.
$user = (new User('Nick'))->setRole($guestRole);

// Adding permissions on user.
// If permissions are set on user, role permissions will not count anymore.
$user->addPermissions(['articles.read']);

// Set current user.
$acl->setCurrentUser($user);

// Adding additional permissions for the current user only.
$acl->addPermissions(['articles.create']);

// Check permissions for current user.
if ($acl->can('articles.read')) {
    // user has permission to read articles.
}

// check permission for specific user.
if ($acl->cant(key: 'articles.read', user: $user)) {
    // user has not permission to read articles.
}
```

# Documentation

## Acl Interface

Following.

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)