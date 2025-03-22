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

use IteratorAggregate;

/**
 * RolesInterface
 */
interface RolesInterface extends IteratorAggregate
{
    /**
     * Add a new role returning a new instance.
     *
     * @param RoleInterface $role
     * @return static
     */
    public function add(RoleInterface $role): static;
    
    /**
     * Remove a role returning a new instance.
     *
     * @param string $role
     * @return static
     */
    public function remove(string $role): static;
    
    /**
     * Returns a new instance with the roles sorted.
     *
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callback): static;
    
    /**
     * Returns a new instance with the filtered roles.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns a new instance with the specified active roles filtered.
     *
     * @param bool $active
     * @return static
     */
    public function active(bool $active = true): static;
    
    /**
     * Returns a new instance with the specified area roles filtered.
     *
     * @param string $area
     * @return static
     */
    public function area(string $area): static;
    
    /**
     * Returns all roles. 
     *
     * @return array<string, RoleInterface>
     */
    public function all(): array;
    
    /**
     * Returns first role or null if none.
     *
     * @return null|RoleInterface
     */
    public function first(): null|RoleInterface;

    /**
     * Returns true if role exists, otherwise false.
     *
     * @return bool
     */
    public function has(string $key): bool;
    
    /**
     * Returns the role by key or null if none.
     *
     * @param string $key
     * @return null|RoleInterface
     */
    public function get(string $key): null|RoleInterface;
    
    /**
     * Get the column of the roles.
     *
     * @param string $column The column such as 'name'.
     * @param null|string $index The index such as 'key'.
     * @return array
     */
    public function column(string $column, null|string $index = null): array;
    
    /**
     * Returns a new instance only with the roles specified.
     *
     * @param array $roles
     * @return static
     */
    public function only(array $roles): static;
        
    /**
     * Returns a new instance except the roles specified.
     *
     * @param array $roles
     * @return static
     */
    public function except(array $roles): static;
}