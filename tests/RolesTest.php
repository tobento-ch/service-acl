<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Acl\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Acl\Role;
use Tobento\Service\Acl\RoleInterface;
use Tobento\Service\Acl\Roles;
use Tobento\Service\Acl\RolesInterface;

class RolesTest extends TestCase
{
    public function testThatImplementsRolesInterface()
    {
        $this->assertInstanceof(
            RolesInterface::class,
            new Roles()
        );
    }

    public function testAddMethod()
    {
        $roles = new Roles();
        $editor = new Role('editor');
        $rolesNew = $roles->add($editor);
        
        $this->assertFalse($roles === $rolesNew);
        
        $this->assertSame($editor, $rolesNew->get('editor'));
    }
    
    public function testRemoveMethod()
    {
        $roles = new Roles(
            new Role('editor'),
            new Role('guest'),
        );
        
        $rolesNew = $roles->remove('editor');
        
        $this->assertFalse($roles === $rolesNew);
        
        $this->assertSame(1, count($rolesNew->all()));
    }
    
    public function testSortMethod()
    {
        $editor = new Role('editor');
        $guest = new Role('guest');
        $roles = new Roles($editor, $guest);
        
        $this->assertSame(['editor', 'guest'], array_keys($roles->all()));
        
        $rolesSorted = $roles->sort(fn(RoleInterface $a, RoleInterface $b): int => $b->name() <=> $a->name());
        
        $this->assertFalse($roles === $rolesSorted);
        
        $this->assertSame(['guest', 'editor'], array_keys($rolesSorted->all()));
    }
    
    public function testFilterMethod()
    {
        $editor = new Role(key: 'editor', active: true);
        $guest = new Role(key: 'guest', active: false);
        $roles = new Roles($editor, $guest);
        
        $this->assertSame(['editor', 'guest'], array_keys($roles->all()));
        
        $rolesFiltered = $roles->filter(fn(RoleInterface $role): bool => $role->active());
        
        $this->assertFalse($roles === $rolesFiltered);
        
        $this->assertSame(['editor'], array_keys($rolesFiltered->all()));
    }
    
    public function testActiveMethod()
    {
        $editor = new Role(key: 'editor', active: true);
        $guest = new Role(key: 'guest', active: false);
        $roles = new Roles($editor, $guest);

        $this->assertFalse($roles === $roles->active());
        $this->assertSame(['editor'], array_keys($roles->active()->all()));
        $this->assertSame(['guest'], array_keys($roles->active(false)->all()));
    }
    
    public function testAreaMethod()
    {
        $editor = new Role(key: 'editor', areas: ['foo', 'bar']);
        $guest = new Role(key: 'guest', areas: ['foo']);
        $roles = new Roles($editor, $guest);

        $this->assertFalse($roles === $roles->area('foo'));
        $this->assertSame(['editor', 'guest'], array_keys($roles->area('foo')->all()));
        $this->assertSame(['editor'], array_keys($roles->area('bar')->all()));
    }

    public function testColumnMethod()
    {
        $this->assertSame([], (new Roles())->column('key'));
        
        $editor = new Role(key: 'editor', name: 'Editor');
        $guest = new Role(key: 'guest', name: 'Guest');
        $roles = new Roles($editor, $guest);
        
        $this->assertSame(['editor', 'guest'], $roles->column('key'));
        $this->assertSame(['editor' => 'Editor', 'guest' => 'Guest'], $roles->column('name', 'key'));
    }
    
    public function testAllMethod()
    {
        $this->assertSame([], (new Roles())->all());
        
        $editor = new Role('editor');
        $guest = new Role('guest');
        $roles = new Roles($editor, $guest);
        
        $this->assertSame(2, count($roles->all()));
        $this->assertInstanceof(RoleInterface::class, $roles->all()['editor'] ?? null);
    }
    
    public function testFirstMethod()
    {
        $this->assertSame(null, (new Roles())->first());
        
        $editor = new Role('editor');
        $guest = new Role('guest');
        $roles = new Roles($editor, $guest);
        
        $this->assertSame($editor, $roles->first());
    }

    public function testHasMethod()
    {        
        $roles = new Roles(new Role('editor'));
        
        $this->assertTrue($roles->has(key: 'editor'));
        $this->assertFalse($roles->has(key: 'guest'));
    }
    
    public function testGetMethod()
    {        
        $editor = new Role('editor');
        $roles = new Roles($editor);
        
        $this->assertSame($editor, $roles->get(key: 'editor'));
        $this->assertSame(null, $roles->get(key: 'guest'));
    }
    
    public function testOnlyMethod()
    {
        $roles = new Roles(
            new Role('editor'),
            new Role('adm'),
            new Role('guest')
        );
        
        $this->assertFalse($roles === $roles->only(['editor']));
        $this->assertSame(['editor'], array_keys($roles->only(['editor'])->all()));
        $this->assertSame(['editor', 'adm'], array_keys($roles->only(['editor', 'adm'])->all()));
        $this->assertSame([], array_keys($roles->only(['foo'])->all()));
    }
    
    public function testExceptMethod()
    {
        $roles = new Roles(
            new Role('editor'),
            new Role('adm'),
            new Role('guest')
        );
        
        $this->assertFalse($roles === $roles->except(['editor']));
        $this->assertSame(['adm', 'guest'], array_keys($roles->except(['editor'])->all()));
        $this->assertSame(['guest'], array_keys($roles->except(['editor', 'adm'])->all()));
        $this->assertSame(['editor', 'adm', 'guest'], array_keys($roles->except(['foo'])->all()));
    }
    
    public function testIterating()
    {
        $roles = new Roles(
            new Role('editor'),
            new Role('adm'),
            new Role('guest')
        );
        
        foreach($roles as $role) {
            $this->assertInstanceof(
                RoleInterface::class,
                $role
            );
        }
    }
}