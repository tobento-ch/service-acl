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
     * @param Authorizable $user
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
     * @param string $key A rule key
     * @return Rule
     */    
    public function rule(string $key): Rule;
        
    /**
     * Adds a rule.
     *
     * @param RuleInterface $rule
     * @return static $this
     */    
    public function addRule(RuleInterface $rule): static;

    /**
     * Check if the given permission are set.
     *
     * @param string $key A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array $parameters Any parameters for custom handler
     * @param null|Authorizable $user If null current user is taken.
     * @return bool True on success, false on failure.
     */    
    public function can(string $key, array $parameters = [], ?Authorizable $user = null): bool;

    /**
     * Check if permission is not given.
     *
     * @param string $key A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array $parameters Any parameters for custom handler
     * @param null|Authorizable $user If null current user is taken.
     * @return bool True no permission, false has permission.
     */    
    public function cant(string $key, array $parameters = [], ?Authorizable $user = null): bool;

    /**
     * Sets the roles.
     *
     * @param array $roles The roles [RoleInterface, ...]
     * @return static $this
     */    
    public function setRoles(array $roles): static;
    
    /**
     * Gets the roles.
     *
     * @param null|string $area An area key such as 'frontend' or null to get all roles.
     * @return array
     */    
    public function getRoles(?string $area = null): array;

    /**
     * Gets the role by key.
     *
     * @param string $key The role key such as 'frontend'.
     * @return null|RoleInterface
     */    
    public function getRole(string $key): ?RoleInterface;

    /**
     * Whether a role by key exists.
     *
     * @param string $key The role key such as 'frontend'.
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
     * Gets a rule or null
     *
     * @param string $ruleKey The rule key. 'user.create'
     * @return null|RuleInterface Null if rule does not exist.
     */    
    public function getRule(string $ruleKey): ?RuleInterface;    
}