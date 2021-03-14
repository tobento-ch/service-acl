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

/**
 * Trait HasRole
 */
trait HasRole
{
    /**
     * @var string
     */    
    protected string $roleKey = '';
    
    /**
     * @var null|RoleInterface
     */    
    protected ?RoleInterface $role = null;

    /**
     * Sets the roleKey
     *
     * @param string
     * @return static $this
     */
    public function setRoleKey(string $roleKey): static
    {
        $this->roleKey = $roleKey;
        return $this;
    }

    /**
     * Gets the roleKey
     *
     * @return string
     */
    public function getRoleKey(): string
    {
        return $this->roleKey;
    }
    
    /**
     * Sets the role.
     *
     * @param null|RoleInterface
     * @return static $this
     */    
    public function setRole(?RoleInterface $role = null): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * If role exists.
     *
     * @return bool True if has role, otherwise false
     */    
    public function hasRole(): bool
    {
        return !is_null($this->role);
    }        

    /**
     * Gets the role.
     *
     * @return RoleInterface
     */    
    public function role(): RoleInterface
    {
        return $this->role;
    }
}