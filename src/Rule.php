<?php

/**
 * TOBENTO
 *
 * @copyright	Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Acl;

use ArgumentCountError;

/**
 * Rule
 */
class Rule implements RuleInterface
{
	/**
	 * @var null|string The title.
	 */	
	protected ?string $title = null;

	/**
	 * @var string The description.
	 */	
	protected string $description = '';

	/**
	 * @var callable The handler.
	 */	
	protected $handler = null;

	/**
	 * @var string The area key.
	 */	
	protected string $area = 'frontend';	

	/**
	 * @var bool If the rule needs permission given.
	 */	
	protected bool $needsPermission = true;
	
	/**
	 * Create a new Rule
	 *
	 * @param string The key such as 'user.create'. Important: use only dot as separater.
	 */	
	public function __construct(
		protected string $key
	) {}

	/**
	 * Get the key.
	 *
	 * @return string The key such as 'user.create'.
	 */	
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * Get the input key. May be used for form input.
	 *
	 * @return string The key such as 'user_create'.
	 */	
	public function getInputKey(): string
	{
		return str_replace(array('.'), array('_'), $this->key);
	}	

	/**
	 * Set a title.
	 *
	 * @param string A title.
	 * @return RuleInterface
	 */	
	public function title(string $title): RuleInterface
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Get the title.
	 *
	 * @return string The title
	 */	
	public function getTitle(): string
	{
		if ($this->title === null) {
			$this->title = $this->key;
		}
		
		return $this->title;
	}

	/**
	 * Set a description.
	 *
	 * @param string A description
	 * @return Rule
	 */	
	public function description(string $description): RuleInterface
	{
		$this->description = $description;
		return $this;
	}
	
	/**
	 * Get the description.
	 *
	 * @return string The description
	 */	
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * Return if the rule matches the criteria.
	 *
	 * @param AclInterface
	 * @param string A permission key 'user.create'.
	 * @param array Any parameters for custom handler
     * @param null|Authorizable
	 * @return bool True if rule matches, otherwise false
	 */	
	public function matches(
		AclInterface $acl,
		string $key,
		array $parameters = [],
		?Authorizable $user = null
	): bool	{
		
		if (!is_null($user))
        {
            $permissions = $this->collectPermissions($user, $acl);
        } 
		else
		{
			$user = $acl->getCurrentUser();
			
			// if no user at all, rule does not match.
            if (is_null($user))
            {
                return false;
            }
			
			// if current user is set, we merge acl permissions.		
			$permissions = array_unique(
				array_merge(
					$acl->getPermissions(),
					$this->collectPermissions($user, $acl)
				)
			);			
        }
		
		// user needs a role.
		if (! $user->hasRole()) {
			return false;
		}
        
		// check if permission is given.
		if ($this->needsPermission)
        {
            // area check
            $allowedAreaKeys = $acl->getAreasToRules()[$user->role()->area()] ?? [];
            
			if ($this->getArea() !== $user->role()->area()
				&& !in_array($this->getArea(), $allowedAreaKeys)
            ) {
				return false;
			}
            
            // permission check
			if (!in_array($key, $permissions))
            {
				return false;
			}
		}
		
		if ($this->handler)
        {
			return $this->callRuleHandler($parameters, $user);
		}
		
		return true;		
	}
	
	/**
	 * Set the handler.
	 *
	 * @param callable
	 * @return RuleInterface
	 *
	 * @callable must return bool. True for permission given, otherwhise false.
	 */	
	public function handler(callable $handler): RuleInterface
	{
		$this->handler = $handler;
		return $this;
	}

	/**
	 * Set the area
	 *
	 * @param string
	 * @return RuleInterface
	 */	
	public function area(string $area): RuleInterface
	{
		$this->area = $area;
		return $this;
	}
	
	/**
	 * Get the area.
	 *
	 * @return string
	 */	
	public function getArea(): string
	{
		return $this->area;
	}
	
	/**
	 * Set if the rule needs permission set on acl to be granted.
	 *
	 * @param bool True needs permission, otherwise false.
	 * @return RuleInterface
	 */	
	public function needsPermission(bool $needsPermission): RuleInterface
	{
		$this->needsPermission = $needsPermission;
		return $this;
	}

	/**
	 * Collect permissions from the user.
	 *
	 * @param Authorizable
	 * @param AclInterface
	 * @return array The permissions such as ['user.create', 'user.update']
	 */	
	protected function collectPermissions(Authorizable $user, AclInterface $acl): array
	{
		// check if user has its own permissions.
		if ($user->hasPermissions())
        {
			return $user->getPermissions();
		}
        
        if (! $user->hasRole())
        {
            return [];
        }
        
        // get permissions from role here, as user might have changed role permissions.
        if ($acl->hasRole($user->role()->key()))
        {        
            return $acl->getRole($user->role()->key())->getPermissions();
        }
        
        return $user->role()->getPermissions();
	}

	/**
	 * Calls the rule handler.
	 *
	 * @param array Any parameters.
     * @param Authorizable
	 * @return bool True on permission given, false on not permission given.
	 */	
	protected function callRuleHandler(array $parameters, Authorizable $user): bool
	{
		if (is_callable($this->handler)) {
			
			try {
                array_unshift($parameters, $user);
				$response = call_user_func_array($this->handler, $parameters);
				return ($response === true || $response === false) ? $response : false;
				
			} catch(ArgumentCountError $e) {
				return false;
			}
		}
		
		return false;
	}	
}