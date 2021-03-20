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
use Tobento\Service\Acl\Test\Mock\Article;
use Tobento\Service\Acl\Authorizable;
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Role;
use Tobento\Service\Acl\Rule;

/**
 * HasPermissionTrait tests
 */
class HasPermissionTraitTest extends TestCase
{
    private $acl;
    
    public function setUp(): void
    {
        $this->acl = new Acl();
    }

    public function testSetAndGetPermissions()
    {
        $acl = new Acl();
       
        $acl->setPermissions(['user.create', 'user.update']);
        
        $this->assertSame(['user.create', 'user.update'], array_values($acl->getPermissions()));
        $this->assertNotSame(['user.create', 'user.read'], array_values($acl->getPermissions()));
    }
    
    public function testAddPermissions()
    {
        $acl = new Acl();
       
        $acl->setPermissions(['user.create', 'user.update']);
        
        $acl->addPermissions(['user.create', 'user.delete']);
        
        $this->assertSame(['user.create', 'user.update', 'user.delete'], array_values($acl->getPermissions()));
        $this->assertNotSame(['user.create', 'user.read'], array_values($acl->getPermissions()));
    }

    public function testHasPermissions()
    {
        $acl = new Acl();
        
        $this->assertFalse($acl->hasPermissions());
        
        $acl->setPermissions(['user.create', 'user.update']);
        
        $acl->addPermissions(['user.create', 'user.delete']);
        
        $this->assertTrue($acl->hasPermissions());
        $this->assertTrue($acl->hasPermission('user.create'));
        $this->assertFalse($acl->hasPermission('user.read'));
    }     
}