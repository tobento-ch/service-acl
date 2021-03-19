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
    private $acl;
    
    public function setUp(): void
    {
        $this->acl = new Acl();
    }

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
        $editor = new Role('editor', ['backend']);
        
        $acl->setRoles([$guest, $editor]);
        
        $roles = $acl->getRoles('backend');
        
        $this->assertSame([], $acl->getRoles('frontend'));
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('admin'));
        $this->assertSame($guest, $roles['guest']);
        $this->assertSame($editor, $roles['editor']);
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

    public function testPermissionsFailsCurrentUserWithNoRole()
    {
        $acl = new Acl();
        
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->addPermissions(['articles.create']);
        
        $acl->setCurrentUser(new User('Nick'));
        
        $this->assertFalse($acl->can('articles.create'));
        $this->assertFalse($acl->can('articles.update'));     
    }

    public function testPermissionsCurrentUserWithRole()
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
        //$this->assertTrue($acl->can('articles.create'));
        //$this->assertFalse($acl->can('articles.update'));     
    }     
}