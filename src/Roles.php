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

use ArrayIterator;
use Traversable;

/**
 * Roles
 */
class Roles implements RolesInterface
{
    /**
     * @var array<string, RoleInterface>
     */
    protected array $roles = [];
    
    /**
     * Create a new Roles instance.
     *
     * @param RoleInterface ...$roles
     */
    public function __construct(
        RoleInterface ...$roles
    ) {
        foreach($roles as $role) {
            $this->roles[$role->key()] = $role;
        }
    }

    /**
     * Add a new role returning a new instance.
     *
     * @param RoleInterface $role
     * @return static
     */
    public function add(RoleInterface $role): static
    {
        $new = clone $this;
        $new->roles[$role->key()] = $role;
        return $new;
    }
    
    /**
     * Remove a role returning a new instance.
     *
     * @param string $role
     * @return static
     */
    public function remove(string $role): static
    {
        $new = clone $this;
        unset($new->roles[$role]);
        return $new;
    }
    
    /**
     * Returns a new instance with the roles sorted.
     *
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callback): static
    {
        $new = clone $this;
        uasort($new->roles, $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with the filtered roles.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->roles = array_filter($this->roles, $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with the specified active roles filtered.
     *
     * @param bool $active
     * @return static
     */
    public function active(bool $active = true): static
    {
        return $this->filter(fn(RoleInterface $role): bool => $role->active() === $active);
    }
    
    /**
     * Returns a new instance with the specified area roles filtered.
     *
     * @param string $area
     * @return static
     */
    public function area(string $area): static
    {
        return $this->filter(fn(RoleInterface $role): bool => in_array($area, $role->areas()));
    }
    
    /**
     * Returns all roles. 
     *
     * @return array<string, RoleInterface>
     */
    public function all(): array
    {
        return $this->roles;        
    }
    
    /**
     * Returns first role or null if none.
     *
     * @return null|RoleInterface
     */
    public function first(): null|RoleInterface
    {
        $key = array_key_first($this->roles);
        
        return is_null($key) ? null : $this->roles[$key];
    }

    /**
     * Returns true if role exists, otherwise false.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->roles[$key]);
    }
    
    /**
     * Returns the role by key or null if none.
     *
     * @param string $key
     * @return null|RoleInterface
     */
    public function get(string $key): null|RoleInterface
    {
        return $this->roles[$key] ?? null;
    }
    
    /**
     * Get the column of the roles.
     *
     * @param string $column The column such as 'name'.
     * @param null|string $index The index such as 'key'.
     * @return array
     */
    public function column(string $column, null|string $index = null): array
    {
        return array_column($this->roles, $column, $index);
    }
    
    /**
     * Returns a new instance only with the roles specified.
     *
     * @param array $roles
     * @return static
     */
    public function only(array $roles): static
    {
        return $this->filter(fn(RoleInterface $role): bool => in_array($role->key(), $roles));
    }
        
    /**
     * Returns a new instance except the roles specified.
     *
     * @param array $roles
     * @return static
     */
    public function except(array $roles): static
    {
        return $this->filter(fn(RoleInterface $role): bool => !in_array($role->key(), $roles));
    }
    
    /**
     * Get the iterator.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {    
        return new ArrayIterator($this->all());
    }
}