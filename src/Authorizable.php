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

/**
 * The Authorizable interface
 */
interface Authorizable extends Permissionable
{
	/**
	 * Check if the given permission are set.
	 *
	 * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
	 * @param array Any parameters for custom handler
	 * @return bool True on success, false on failure.
	 */	
	public function can(string $key, array $parameters = []): bool;

	/**
	 * Check if permission is not given.
	 *
	 * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
	 * @param array Any parameters for custom handler
	 * @return bool True no permission, false has permission.
	 */	
	public function cant(string $key, array $parameters = []): bool;
    
	/**
	 * Sets the roleKey
	 *
	 * @param string
	 * @return static $this
	 */
	public function setRoleKey(string $roleKey): static;

	/**
	 * Gets the roleKey
	 *
	 * @return string
	 */
	public function getRoleKey(): string;
    
	/**
	 * Sets the role.
	 *
	 * @param null|RoleInterface
	 * @return static $this
	 */	
	public function setRole(?RoleInterface $role = null): static;

	/**
	 * If role exists.
	 *
	 * @return bool True if has role, otherwise false
	 */	
	public function hasRole(): bool;		

	/**
	 * Gets the role.
	 *
	 * @return RoleInterface
	 */	
	public function role(): RoleInterface;
}