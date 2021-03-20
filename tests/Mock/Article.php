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

namespace Tobento\Service\Acl\Test\Mock;

use Tobento\Service\Acl\Authorizable;

/**
 * Article
 */
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