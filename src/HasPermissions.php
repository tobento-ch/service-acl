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
 * Trait HasPermissions
 */
trait HasPermissions
{
    /**
     * @var array The permissions.
     */    
    protected array $permissions = [];

    /**
     * Set the permissions.
     *
     * @param array $permissions The permissions ['user.create', 'user.update']
     * @return static $this
     */    
    public function setPermissions(array $permissions): static
    {
        $this->permissions = $permissions;
        
        return $this;
    }
    
    /**
     * Add permissions.
     *
     * @param array $permissions The permissions ['user.create', 'user.update']
     * @return static $this
     */    
    public function addPermissions(array $permissions): static
    {
        $this->permissions = array_unique(array_merge($this->permissions, $permissions));
        
        return $this;
    }
    
    /**
     * Remove permissions.
     *
     * @param array $permissions The permissions ['user.create', 'user.update'] to remove.
     * @return static $this
     */
    public function removePermissions(array $permissions): static
    {
        $this->permissions = array_diff($this->permissions, $permissions);
        
        return $this;
    }

    /**
     * Get the permissions.
     *
     * @return array The permissions ['user.create', 'user.update']
     */    
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Has permissions.
     *
     * @return bool
     */    
    public function hasPermissions(): bool
    {
        return !empty($this->permissions);
    }

    /**
     * Has the permission.
     *
     * @return bool $key True on success, otherwise false.
     */    
    public function hasPermission(string $key): bool
    {
        return in_array($key, $this->permissions);
    }    
}