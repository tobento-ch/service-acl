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
 * AclInterface
 */
interface AclInterface extends Permissionable
{
    /**
     * Set the current user.
     *
     * @param Authorizable
     * @return static $this
     */    
    public function setCurrentUser(Authorizable $user): static;
    
    /**
     * Get the current user.
     *
     * @return null|Authorizable
     */    
    public function getCurrentUser(): ?Authorizable;    

    /**
     * Verifies input permissions.
     *
     * @param array The input permissions ['user_create' => 0, 'user_update' => 1]
     * @param RoleInterface
     * @return The verified permissions ['user_create', 'user_update']
     */    
    public function verifyInputPermissions(array $permissions, RoleInterface $role): array;

    /**
     * Create and adds a new Rule.
     *
     * @param string A rule key
     * @return Rule
     */    
    public function rule(string $key): Rule;
        
    /**
     * Adds a rule.
     *
     * @param RuleInterface
     * @return static $this
     */    
    public function addRule(RuleInterface $rule): static;

    /**
     * Check if the given permission are set.
     *
     * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array Any parameters for custom handler
     * @param null|Authorizable If null current user is taken.
     * @return bool True on success, false on failure.
     */    
    public function can(string $key, array $parameters = [], ?Authorizable $user = null): bool;

    /**
     * Check if permission is not given.
     *
     * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array Any parameters for custom handler
     * @param null|Authorizable If null current user is taken.
     * @return bool True no permission, false has permission.
     */    
    public function cant(string $key, array $parameters = [], ?Authorizable $user = null): bool;

    /**
     * Sets the roles.
     *
     * @param array The roles.
     * @param null|string An area key such as 'frontend'. If null it takes the area from the role.
     * @return static $this
     */    
    public function setRoles(array $roles, ?string $area = null): static;
    
    /**
     * Gets the roles.
     *
     * @param string An area key such as 'frontend'
     * @return array
     */    
    public function getRoles(string $area): array;

    /**
     * Gets the role by id.
     *
     * @param int|string The role id or key.
     * @return null|RoleInterface
     */    
    public function getRole(int|string $roleIdOrKey): ?RoleInterface;

    /**
     * Gets the role by id.
     *
     * @param int|string The role id or key.
     * @return bool If role exists.
     */    
    public function hasRole(int|string $roleIdOrKey): bool;
        
    /**
     * Gets the rules.
     *
     * @return array The rules
     */    
    public function getRules(): array;

    /**
     * Sets the areas.
     *
     * @param array The areas such as ['frontend' => 1, ...]
     * @return static $this
     */    
    public function setAreas(array $areas): static;

    /**
     * Gets the area key by its id.
     *
     * @param int The area id.
     * @return null|string The area key such as 'frontend', or null if not exist.
     */    
    public function getAreaKey(int $id): ?string;

    /**
     * Sets the areas to rules areas allowed.
     *
     * @param array ['frontend' => ['frontend'], 'backend' => ['backend', 'frontend']]
     * @return static $this
     */    
    public function setAreasToRules(array $areasToRules): static;

    /**
     * Gets the areas to rules.
     *
     * @return array ['frontend' => ['frontend'], 'backend' => ['backend', 'frontend']]
     */    
    public function getAreasToRules(): array;
}