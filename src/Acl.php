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
 * Acl
 */
class Acl implements AclInterface
{
    use HasPermissions;
    
    /**
     * @var null|Authorizable
     */    
    protected ?Authorizable $currentUser = null;
    
    /**
     * @var array The rules.
     */    
    protected array $rules = [];
    
    /**
     * @var array The roles.
     */    
    protected array $roles = [];

    /**
     * @var array The roles by area.
     */    
    protected array $rolesByArea = [];   

    /**
     * Set the current user.
     *
     * @param Authorizable
     * @return static $this
     */    
    public function setCurrentUser(Authorizable $user): static
    {
        $this->currentUser = $user;
        
        return $this;
    }
 
    /**
     * Get the current user.
     *
     * @return null|Authorizable
     */    
    public function getCurrentUser(): ?Authorizable
    {
        return $this->currentUser;
    } 

    /**
     * Create and adds a new Rule.
     *
     * @param string A rule key
     * @return Rule
     */    
    public function rule(string $key): Rule
    {
        $rule = new Rule($key);
        
        $this->addRule($rule);
        
        return $rule;
    }
    
    /**
     * Adds a rule.
     *
     * @param RuleInterface
     * @return static $this
     */    
    public function addRule(RuleInterface $rule): static
    {
        $this->rules[$rule->getKey()] = $rule;
        
        return $this;
    }

    /**
     * Check if the given permission are set.
     *
     * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array Any parameters for custom handler
     * @param null|Authorizable If null current user is taken.
     * @return bool True on success, false on failure.
     */    
    public function can(string $key, array $parameters = [], ?Authorizable $user = null): bool
    {        
        if (! str_contains($key, '|'))
        {
            return (bool) $this->getRule($key)?->matches($this, $key, $parameters, $user);
        }
        
        foreach(explode('|', $key) as $key)
        {
            if ($this->can($key, $parameters[$key] ?? [], $user) === false)
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if permission is not given.
     *
     * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array Any parameters for custom handler
     * @param null|Authorizable If null current user is taken.
     * @return bool True no permission, false has permission.
     */    
    public function cant(string $key, array $parameters = [], ?Authorizable $user = null): bool
    {
        return ! $this->can($key, $parameters, $user);
    }
    
    /**
     * Sets the roles.
     *
     * @param array The roles [RoleInterface, ...]
     * @return static $this
     */    
    public function setRoles(array $roles): static
    {
        foreach($roles as $role)
        {
            if (! $role instanceof RoleInterface) {
                continue;
            }
            
            foreach($role->areas() as $area)
            {
                $this->rolesByArea[$area][$role->key()] = $role;    
            }
            
            $this->roles[$role->key()] = $role;
        }
        
        return $this;
    }
    
    /**
     * Gets the roles.
     *
     * @param null|string An area key such as 'frontend' or null to get all roles.
     * @return array
     */    
    public function getRoles(?string $area = null): array
    {
        if (is_null($area)) {
            return $this->roles;
        }
        
        return $this->rolesByArea[$area] ?? [];
    }

    /**
     * Gets the role by key.
     *
     * @param string The role key such as 'frontend'.
     * @return null|RoleInterface
     */    
    public function getRole(string $key): ?RoleInterface
    {
        return $this->roles[$key] ?? null;
    }

    /**
     * Whether a role by key exists.
     *
     * @param string The role key such as 'frontend'.
     * @return bool If role exists.
     */    
    public function hasRole(string $key): bool
    {
        return isset($this->roles[$key]);
    }
        
    /**
     * Gets the rules.
     *
     * @return array The rules
     */    
    public function getRules(): array
    {
        return $this->rules;
    }  
    
    /**
     * Gets a rule or null
     *
     * @param string The rule key. 'user.create'
     * @return null|RuleInterface Null if rule does not exist.
     */    
    protected function getRule(string $ruleKey): ?RuleInterface
    {
        return $this->rules[$ruleKey] ?? null;
    }
}