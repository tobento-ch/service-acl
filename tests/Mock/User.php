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
use Tobento\Service\Acl\AuthorizableAware;

/**
 * User
 */
class User implements Authorizable
{
    use AuthorizableAware;
    
    public function __construct(
        protected string $name,
    ) {}
    
    public function name(): string
    {
        return $this->name;
    }    
}