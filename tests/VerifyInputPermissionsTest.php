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
use Tobento\Service\Acl\VerifyInputPermissions;
use Tobento\Service\Acl\Authorizable;
use Tobento\Service\Acl\Acl;
use Tobento\Service\Acl\Role;
use Tobento\Service\Acl\Rule;

/**
 * VerifyInputPermissionsTest tests
 */
class VerifyInputPermissionsTest extends TestCase
{
    public function testVerifyPermissionsWithNumber()
    {
        $acl = new Acl();
        $acl->rule('articles.read');
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->rule('articles.delete');
        
        $editor = new Role('editor');
        
        $verifier = new VerifyInputPermissions($acl);
        
        $verifiedPermissions = $verifier->verify(
            ['articles_read' => 0, 'articles_create' => 1],
            $editor
        );        
        
        $this->assertSame(['articles.create'], $verifiedPermissions);
    }

    public function testVerifyPermissionsWithBool()
    {
        $acl = new Acl();
        $acl->rule('articles.read');
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->rule('articles.delete');
        
        $editor = new Role('editor');
        
        $verifier = new VerifyInputPermissions($acl);
        
        $verifiedPermissions = $verifier->verify(
            ['articles_read' => false, 'articles_create' => true],
            $editor
        );        
        
        $this->assertSame(['articles.create'], $verifiedPermissions);
    }

    public function testVerifyPermissionsWithAreaSpecific()
    {
        $acl = new Acl();
        $acl->rule('articles.read')->area('frontend');
        $acl->rule('articles.create')->area('backend');
        $acl->rule('articles.update')->area('backend');
        $acl->rule('articles.delete')->area('backend');
        
        $editor = new Role('editor', ['backend']);
        
        $verifier = new VerifyInputPermissions($acl);
        
        $verifiedPermissions = $verifier->verify(
            ['articles_read' => true, 'articles_create' => true, 'articles_update' => false],
            $editor
        );        
        
        $this->assertSame(['articles.create'], $verifiedPermissions);
    }

    public function testVerifyPermissionsWithInvalidArrayIndexes()
    {
        $acl = new Acl();
        $acl->rule('articles.read');
        $acl->rule('articles.create');
        $acl->rule('articles.update');
        $acl->rule('articles.delete');
        
        $editor = new Role('editor');
        
        $verifier = new VerifyInputPermissions($acl);
        
        $verifiedPermissions = $verifier->verify(
            [0 => '', 1 => []],
            $editor
        );        
        
        $this->assertSame([], $verifiedPermissions);
    }    
}