# ACL Service

The ACL Service is a simple role and user-level access control system.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
	- [Simple Example](#simple-example)
- [Documentation](#documentation)
	- [Rules](#rules)
        - [Default Rule](#default-rule)
        - [Default Rule Custom](#default-rule-custom)
        - [Custom Rules](#custom-rules)
	- [Permissions](#permissions)
	- [Roles](#roles)   
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

## Rules

Adding and getting rules.

```php
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\RuleInterface;

// Create Acl.
$acl = new Acl();

// Add default rule.
$acl->rule('articles.read');

// Add custom rule.
$acl->addRule(RuleInterface $rule);

// Get rules.
foreach($acl->getRules() as $rule)
{
    $key = $rule->getKey();
    $inputKey = $rule->getInputKey();
    $title = $rule->getTitle();
    $description = $rule->getDescription();
    $area = $rule->getArea();
}

// get specific rules
$rule = $acl->getRule('articles.read');
```

### Default Rule

The default rule has the following permission behaviour:

```php
use Tobento\Service\Acl\Acl;

// Create Acl.
$acl = new Acl();

$acl->rule('articles.read');
$acl->rule('articles.update');

// Create role.
$role = new Role('guest');

// Create and set user role.
$user = (new User('Nick'))->setRole($role);

// Adding permissions on acl, only for current user.
$acl->addPermissions(['articles.read']);

// Adding permissions on role.
$role->addPermissions(['articles.read']);

// Adding permissions on user.
// If permissions are set on user, role permissions will not count anymore.
// Only acl and user specific permissions.
$user->addPermissions(['articles.read']);
```

Areas bahviour:

```php
use Tobento\Service\Acl\Acl;

// Create Acl.
$acl = new Acl();

$acl->rule('articles.read', 'frontend');
$acl->rule('articles.update', 'backend');

// Guest Role taking only frontend rules into account,
// ignoring any permission from backend rules even if permission is given.
$role = new Role('guest', ['frontend']);

// Editor can have frontend and backend rules.
$role = new Role('editor', ['frontend', 'backend']);
```

### Default Rule Custom

You can easily add a custom handler for extending a specific rule behaviour.

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

// Article class example
class Article
{    
    public function __construct(
        protected string $name,
        protected array $roles = [],
        protected null|Authorizable $user = null
    ) {}

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getUser(): null|Authorizable
    {
        return $this->user;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }    
}

// Create Acl.
$acl = new Acl();

// Rule to check if user is allowed to access a specific resource.
$acl->rule('resource')
    ->needsPermission(false)
    ->handler(function(Authorizable $user, null|Authorizable $resourceUser): bool {

        if (is_null($resourceUser)) {
            return false;
        }

        return $user === $resourceUser;
    });

// Rule to check if user has role for a specific resource.
$acl->rule('has_role')
    ->needsPermission(false)
    ->handler(function(Authorizable $user, array $roles = []) {

        if (empty($roles)) {
            return true;
        }

        return in_array($user->role()->key(), $roles);                    
    });
            
$user = (new User('Nick'))->setRole(new Role('editor'));

$acl->setCurrentUser($user);    

$article = new Article('About us', ['editor'], $user);

// Check resource access.
if ($acl->can('resource', [$article->getUser()])) {
    // user can access about page.
}

// Check resource role access.
if ($acl->can('has_role', [$article->getRoles()])) {
    // user has the right role to access this resource.
}
```

### Custom Rules

You can easily add a custom rule for a different permission strategy.

Your Rule must implement the following RuleInterface.

```php
/**
 * RuleInterface
 */
interface RuleInterface
{
    /**
     * Get the key.
     *
     * @return string The key such as 'user.create'.
     */    
    public function getKey(): string;

    /**
     * Get the input key. May be used for form input.
     *
     * @return string The key such as 'user_create'.
     */    
    public function getInputKey(): string;    
    
    /**
     * Get the title.
     *
     * @return string The title
     */    
    public function getTitle(): string;
    
    /**
     * Get the description.
     *
     * @return string The description
     */    
    public function getDescription(): string;
    
    /**
     * Get the area.
     *
     * @return string
     */    
    public function getArea(): string;

    /**
     * If the rule requires permissions to match the rule.
     *
     * @return bool
     */    
    public function requiresPermission(): bool;    

    /**
     * Return if the rule matches the criteria.
     *
     * @param AclInterface
     * @param string A permission key 'user.create'.
     * @param array Any parameters for custom handler
     * @param null|Authorizable
     * @return bool True if rule matches, otherwise false
     */    
    public function matches(
        AclInterface $acl,
        string $key,
        array $parameters = [],
        ?Authorizable $user = null
    ): bool;    
}
```

Lets make a custum rule for just letting user specific permissions have access ignoring acl and role permissions.

```php
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\AclInterface;
use Tobento\Service\Acl\RuleInterface;
use Tobento\Service\Acl\Authorizable;
use Tobento\Service\Acl\AuthorizableAware;
use Tobento\Service\Acl\Role;

// Custom rule
class CustomRule implements RuleInterface
{    
    public function __construct(
        protected string $key,
        protected string $area,
    ) {}
 
    public function getKey(): string
    {
        return $this->key;
    }

    public function getInputKey(): string
    {
        return $this->key;
    }
       
    public function getTitle(): string
    {        
        return $this->key;
    }
   
    public function getDescription(): string
    {
        return '';
    }
 
    public function getArea(): string
    {
        return $this->area;
    }
   
    public function requiresPermission(): bool
    {
        return true;
    }
 
    public function matches(
        AclInterface $acl,
        string $key,
        array $parameters = [],
        ?Authorizable $user = null
    ): bool {
            
        $user = $user ?: $acl->getCurrentUser();

        // not user at all
        if (is_null($user)) {
            return false;
        }
        
        // user needs a role.
        if (! $user->hasRole()) {
            return false;
        }

        // collect only user permissions.
        if (!$user->hasPermissions()) {
            return false;
        }
        
        $permissions = $user->getPermissions();

        // permission check
        if (!in_array($key, $permissions)) {
            return false;
        }
        
        // area check
        if (!in_array($this->getArea(), $user->role()->areas())) {
            return false;
        }
        
        return true;
    }
}

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

// Adding default rules.
$acl->addRule(new CustomRule('articles.read', 'frontend'));
$acl->addRule(new CustomRule('articles.create', 'frontend'));

// Create role.
$role = new Role('guest');

// Adding permissions on role does has no effect.
$role->addPermissions(['articles.read']);

// Create and set user role.
$user = (new User('Nick'))->setRole($role);
$user->addPermissions(['articles.create']);

// Set current user.
$acl->setCurrentUser($user);

if ($acl->can('articles.create')) {
    // user has permission to read articles.
}
```

## Permissions

The following methods are available on objects implementing the Permissionable Interface or the Authorizable Interface.

- Tobento\Service\Acl\Acl::class
- Tobento\Service\Acl\Role::class

```php
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Permissionable;
use Tobento\Service\Acl\Authorizable;

// Create Acl.
$acl = new Acl();

// Set all permissions.
$acl->setPermissions(['user.create', 'user.update']);

// Adding more permissions.
$acl->addPermissions(['user.delete']);

$permissions = $acl->getPermissions(); // ['user.create', 'user.update', 'user.delete']

// Has any permissions at all.
$hasPermissions = $acl->hasPermissions();

// Has specific permission.
$hasPermission = $acl->hasPermission('user.update');
```

## Roles

Working with roles.

```php
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Role;
use Tobento\Service\Acl\RoleInterface;

// Create Acl.
$acl = new Acl();

// Set roles on acl for later reusage if needed.
// Role must implement RoleInterface, otherwise it is ignored.
$acl->setRoles([
    new Role('guest'),
    new Role('editor'),
]);

// Get roles.
foreach($acl->getRoles() as $role)
{
    $key = $role->key();
    $active = $role->active();
    $areas = $role->areas();
    $name = $role->name();
}

// Get Specific role.
$role = $acl->getRole('editor');

// Check if role exists.
if ($acl->hasRole('editor')) {
    // editor role exists.
}
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)