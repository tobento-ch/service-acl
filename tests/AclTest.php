<?php

/**
 * TOBENTO
 *
 * @copyright    Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Acl\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Acl\Test\Mock\User;
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Role;
use Tobento\Service\Acl\Rule;

/**
 * Acl tests
 */
class AclTest extends TestCase
{
    public function testSetAndGetCurrentUser()
    {
        $acl = new Acl();
        
        $user = new User('Nick');
        
        $acl->setCurrentUser($user);
        
        $this->assertSame($user, $acl->getCurrentUser());                
    }

    public function testAddingRule()
    {
        $acl = new Acl();
        
        $rule = $acl->rule('articles.read')
                    ->title('Article Read')
                    ->description('If a user can read articles');
        
        $this->assertSame('articles.read', $rule->getKey());
        $this->assertSame('Article Read', $rule->getTitle());
        $this->assertSame('If a user can read articles', $rule->getDescription());
    }

    public function testAddRule()
    {
        $acl = new Acl();
        
        $rule = new Rule('articles.read');
        
        $acl->addRule($rule);
        
        $this->assertSame($rule, $acl->getRules()['articles.read']);
    }

    public function testSetRolesWithDefaultArea()
    {
        $acl = new Acl();
        
        $guest = new Role('guest');
        $editor = new Role('editor');
        
        $acl->setRoles([$guest, $editor]);
        
        $roles = $acl->getRoles('frontend');
        
        $this->assertSame([], $acl->getRoles('backend'));
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('admin'));
        $this->assertSame($guest, $roles['guest']);
        $this->assertSame($editor, $roles['editor']);
        $this->assertSame($guest, $acl->getRole('guest'));
        $this->assertSame($editor, $acl->getRole('editor'));
    }

    public function testSetRolesWithSpecificArea()
    {
        $acl = new Acl();
        
        $guest = new Role('guest', ['backend']);
        $editor = new Role('editor', ['backend', 'api']);
        
        $acl->setRoles([$guest, $editor]);
        
        $roles = $acl->getRoles('backend');
        $apiRoles = $acl->getRoles('api');
        
        $this->assertSame([], $acl->getRoles('frontend'));
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('admin'));
        $this->assertSame($guest, $roles['guest']);
        $this->assertSame($editor, $roles['editor']);
        $this->assertSame($editor, $apiRoles['editor']);
        $this->assertSame($guest, $acl->getRole('guest'));
        $this->assertSame($editor, $acl->getRole('editor'));
    }
    
    public function testPermissionsFailsIfCurrentUserIsNotSet()
    {
        $acl = new Acl();
        
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->addPermissions(['articles.create']);
        
        $this->assertFalse($acl->can('articles.create'));
        $this->assertFalse($acl->can('articles.update'));     
    }

    public function testPermissionsFailsIfCurrentUserWithNoRole()
    {
        $acl = new Acl();
        
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->addPermissions(['articles.create']);
        
        $acl->setCurrentUser(new User('Nick'));
        
        $this->assertFalse($acl->can('articles.create'));
        $this->assertFalse($acl->can('articles.update'));     
    }

    public function testPermissionsCurrentUser()
    {
        $acl = new Acl();
                
        $acl->rule('articles.read')->area('frontend');
        $acl->rule('articles.create')->area('backend');
        
        $acl->addPermissions(['articles.read', 'articles.create']);
                
        $guestRole = new Role('guest', ['frontend']);
        $editorRole = new Role('editor', ['backend']);
        
        $user = (new User('Nick'))->setRole($guestRole);
        
        $acl->setCurrentUser($user);
        
        $this->assertTrue($acl->can('articles.read'));
        $this->assertFalse($acl->can('articles.update'));     
    }

    public function testPermissionsCurrentUserWithRolePermission()
    {
        $acl = new Acl();
                
        $acl->rule('articles.read')->area('frontend');
        $acl->rule('articles.create')->area('backend');
        $acl->rule('articles.update')->area('backend');
        $acl->rule('articles.delete')->area('backend');
        
        $acl->addPermissions(['articles.read', 'articles.create']);
                
        $role = new Role('editor', ['backend', 'frontend']);
        $role->addPermissions(['articles.update']);
        
        $user = (new User('Nick'))->setRole($role);
        
        $acl->setCurrentUser($user);
        
        $this->assertTrue($acl->can('articles.read'));
        $this->assertTrue($acl->can('articles.update'));
        $this->assertFalse($acl->can('articles.delete'));
    }

    public function testPermissionsCurrentUserWithRoleAndUserPermission()
    {
        $acl = new Acl();
                
        $acl->rule('articles.read')->area('frontend');
        $acl->rule('articles.create')->area('backend');
        $acl->rule('articles.update')->area('backend');
        $acl->rule('articles.delete')->area('backend');
        
        $acl->addPermissions(['articles.read', 'articles.create']);
                
        $role = new Role('editor', ['backend', 'frontend']);
        $role->addPermissions(['articles.update']);
        
        $user = (new User('Nick'))->setRole($role);
        $user->addPermissions(['articles.delete']);
        
        $acl->setCurrentUser($user);
        
        $this->assertTrue($acl->can('articles.read'));
        $this->assertFalse($acl->can('articles.update')); // false as user permission
        $this->assertTrue($acl->can('articles.delete'));
    }

    public function testPermissionsSepecificUser()
    {
        $acl = new Acl();
                
        $acl->rule('articles.read')->area('frontend');
        $acl->rule('articles.create')->area('backend');
        $acl->rule('articles.update')->area('backend');
        $acl->rule('articles.delete')->area('backend');
        
        $acl->addPermissions(['articles.read', 'articles.create']);
                
        $role = new Role('editor', ['backend', 'frontend']);
        $role->addPermissions(['articles.update']);
        
        $user = (new User('Nick'))->setRole($role);
        $user->addPermissions(['articles.delete']);
        
        $this->assertFalse($acl->can(key: 'articles.read', user: $user));
        $this->assertFalse($acl->can(key: 'articles.update', user: $user));
        $this->assertTrue($acl->can(key: 'articles.delete', user: $user));
    }

    public function testMulitplePermissionsOnCanMethod()
    {
        $acl = new Acl();
        
        $acl->rule('articles.read');
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->rule('articles.delete');
        
        $acl->addPermissions(['articles.read', 'articles.create']);
        
        $user = (new User('Nick'))->setRole(new Role('editor', ['frontend']));
        $acl->setCurrentUser($user);
        
        $tom = (new User('Tom'))->setRole(new Role('editor', ['frontend']));
        $tom->addPermissions(['articles.read', 'articles.create']);
        
        $this->assertTrue($acl->can('articles.read|articles.create'));
        $this->assertFalse($acl->can('articles.read|articles.delete'));
        
        $this->assertFalse($acl->can(key: 'articles.read|articles.create', user: $user));
        $this->assertFalse($acl->can(key: 'articles.read|articles.delete', user: $user));
        
        $this->assertTrue($acl->can(key: 'articles.read|articles.create', user: $tom));
        $this->assertFalse($acl->can(key: 'articles.read|articles.delete', user: $tom));
    }     
}