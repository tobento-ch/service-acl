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

use function Tobento\Service\Acl\acl;

/**
 * Trait AuthorizableAware
 */
trait AuthorizableAware
{
	use HasPermissions;
	use HasRole;

	/**
	 * Check if the given permission are set.
	 *
	 * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
	 * @param array Any parameters for custom handler
	 * @return bool True on success, false on failure.
	 */	
	public function can(string $key, array $parameters = []): bool
	{		
		return acl()->can($key, $parameters, $this);
	}

	/**
	 * Check if permission is not given.
	 *
	 * @param string A permission key 'user.create' or multiple keys 'user.create|user.update'
	 * @param array Any parameters for custom handler
	 * @return bool True no permission, false has permission.
	 */	
	public function cant(string $key, array $parameters = []): bool
	{
		return ! $this->can($key, $parameters, $this);
	}    
}