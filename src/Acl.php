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
     * @var array The areas such as ['frontend' => 1, ...]
     */    
    protected array $areas = [];

    /**
     * @var array The areas to rules such as ['frontend' => ['frontend'], 'backend' => ['backend', 'frontend']]
     */    
    protected array $areasToRules = [];    

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
     * @param array The roles.
     * @return static $this
     */    
    public function setRoles(array $roles): static
    {
        foreach($roles as $role)
        {
            foreach($role->areas() as $area)
            {
                $this->rolesByArea[$area][$role->id()] = $role;    
            }
                            
            $this->roles['id'][$role->id()] = $role;
            $this->roles['key'][$role->key()] = $role;
        }
        
        return $this;
    }
    
    /**
     * Gets the roles.
     *
     * @param string An area key such as 'frontend'
     * @return array
     */    
    public function getRoles(string $area): array
    {
        return $this->rolesByArea[$area] ?? [];
    }

    /**
     * Gets the role by id.
     *
     * @param int|string The role id or key.
     * @return null|RoleInterface
     */    
    public function getRole(int|string $roleIdOrKey): ?RoleInterface
    {
        if (is_int($roleIdOrKey)) {
            
            return $this->roles['id'][$roleIdOrKey] ?? null;
        }

        if (is_string($roleIdOrKey)) {
            
            return $this->roles['key'][$roleIdOrKey] ?? null;
        }
                
        return null;
    }

    /**
     * Gets the role by id.
     *
     * @param int|string The role id or key.
     * @return bool If role exists.
     */    
    public function hasRole(int|string $roleIdOrKey): bool
    {
        if (is_int($roleIdOrKey)) {
            
            return isset($this->roles['id'][$roleIdOrKey]);
        }

        if (is_string($roleIdOrKey)) {
            
            return isset($this->roles['key'][$roleIdOrKey]);
        }
            
        return false;
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
     * Sets the areas.
     *
     * @param array The areas such as ['frontend' => 1, ...]
     * @return static $this
     */    
    public function setAreas(array $areas): static
    {
        $this->areas = $areas;
        
        return $this;
    }

    /**
     * Gets the area key by its id.
     *
     * @param int The area id.
     * @return null|string The area key such as 'frontend', or null if not exist.
     */    
    public function getAreaKey(int $id): ?string
    {
        $areas = array_flip($this->areas);
        return $areas[$id] ?? null;
    }    

    /**
     * Sets the areas to rules areas allowed.
     *
     * @param array ['frontend' => ['frontend'], 'backend' => ['backend', 'frontend']]
     * @return static $this
     */    
    public function setAreasToRules(array $areasToRules): static
    {
        $this->areasToRules = $areasToRules;
        
        return $this;
    }

    /**
     * Gets the areas to rules.
     *
     * @return array ['frontend' => ['frontend'], 'backend' => ['backend', 'frontend']]
     */    
    public function getAreasToRules(): array
    {
        return $this->areasToRules;
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