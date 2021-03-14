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

namespace Tobento\Service\Acl;

use Tobento\Service\Acl\RoleInterface;
use Tobento\Service\Acl\HasPermissions;

/**
 * Role
 */
class Role implements RoleInterface
{    
    use HasPermissions;

    /**
     * Create a new Role
     *
     * @param int A role id.
     * @param string A role key such as 'editor'.
     * @param string The area for the role such as 'frontend'
     * @param bool If the role is active.
     * @param int A role name such as 'Editor'.
     */    
    public function __construct(
        protected int $id,
        protected string $key,
        protected string $area = 'frontend',
        protected bool $active = true,
        protected null|string $name = null,
    ) {}
    
    /**
     * Get the id
     *
     * @return string
     */
    public function id(): int
    {
        return $this->id;
    }
    
    /**
     * Get the key
     *
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * If the role is active
     *
     * @return bool
     */
    public function active(): bool
    {
        return $this->active;
    }

    /**
     * Get the area
     *
     * @return string
     */
    public function area(): string
    {
        return $this->area;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name ?: ucfirst($this->key());
    }    
}