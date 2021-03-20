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
 * AclRuleHandlerTest tests
 */
class AclRuleHandlerTest extends TestCase
{
    public function testResourceBelongsToSpecificUser()
    {
        $acl = new Acl();
       
        $acl->rule('resource')
            ->needsPermission(false)
            ->handler(function(Authorizable $user, null|Authorizable $resourceUser): bool {

                if (is_null($resourceUser)) {
                    return false;
                }

                return $user === $resourceUser;
            });

        $nick = (new User('Nick'))->setRole(new Role('editor'));
        $tom = (new User('Tom'))->setRole(new Role('editor'));
        
        $acl->setCurrentUser($nick);    

        $about = new Article('About us', [], $nick);
        $team = new Article('Team');
        
        $this->assertTrue($acl->can('resource', [$about->getUser()]));
        $this->assertFalse($acl->can('resource', [$team->getUser()]));
        
        $this->assertTrue($acl->can('resource', [$about->getUser()], $nick));
        $this->assertFalse($acl->can('resource', [$team->getUser()], $nick));
        
        $this->assertFalse($acl->can('resource', [$about->getUser()], $tom));
        $this->assertFalse($acl->can('resource', [$team->getUser()], $tom));
    }

    public function testResourceOnlyForASpecificRole()
    {
        $acl = new Acl();
       
        // rule to check if user has role.
        $acl->rule('has_role')
            ->needsPermission(false)
            ->handler(function(Authorizable $user, array $roles = []) {

                if (empty($roles)) {
                    return true;
                }

                return in_array($user->role()->key(), $roles);                    
            });

        $nick = (new User('Nick'))->setRole(new Role('registered'));
        
        $acl->setCurrentUser($nick);    

        $about = new Article('About us');
        $team = new Article('Team', ['registered']);
        $info = new Article('Info', ['business']);
        
        // True as empty roles is true on rule.
        $this->assertTrue($acl->can('has_role', [$about->getRoles()]));
        
        $this->assertTrue($acl->can('has_role', [$team->getRoles()]));
        $this->assertFalse($acl->can('has_role', [$info->getRoles()]));
    }

    public function testMulitplePermissionsOnCanMethod()
    {
        $acl = new Acl();
        $acl->rule('articles.read');
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->rule('articles.delete');
        
        $acl->rule('resource')
            ->needsPermission(false)
            ->handler(function(Authorizable $user, null|Authorizable $resourceUser): bool {

                if (is_null($resourceUser)) {
                    return false;
                }

                return $user === $resourceUser;
            });

        $nick = (new User('Nick'))->setRole(new Role('editor'));
        $tom = (new User('Tom'))->setRole(new Role('editor'));
        
        $acl->setCurrentUser($nick);
        $acl->addPermissions(['articles.read']);

        $about = new Article('About us', [], $nick);
        $team = new Article('Team');
        
        $this->assertTrue(
            $acl->can('articles.read|resource', ['resource' => [$about->getUser()]])
        );
        
        $this->assertFalse(
            $acl->can('articles.create|resource', ['resource' => [$about->getUser()]])
        );

        $this->assertFalse(
            $acl->can('articles.create|resource', ['resource' => [$about->getUser()]])
        );
    }    
}