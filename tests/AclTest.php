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
 
}