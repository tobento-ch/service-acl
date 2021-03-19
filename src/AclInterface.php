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
     * @return static $this
     */    
    public function setRoles(array $roles): static;
    
    /**
     * Gets the roles.
     *
     * @param null|string An area key such as 'frontend' or null to get all roles.
     * @return array
     */    
    public function getRoles(?string $area = null): array;

    /**
     * Gets the role by key.
     *
     * @param string The role key such as 'frontend'.
     * @return null|RoleInterface
     */    
    public function getRole(string $key): ?RoleInterface;

    /**
     * Whether a role by key exists.
     *
     * @param string The role key such as 'frontend'.
     * @return bool If role exists.
     */    
    public function hasRole(string $key): bool;
        
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