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
 * RuleInterface
 */
interface RuleInterface
{
	/**
	 * Get the key.
	 *
	 * @return string The key such as 'user.create'.
	 */	
	public function getKey(): string;

	/**
	 * Get the input key. May be used for form input.
	 *
	 * @return string The key such as 'user_create'.
	 */	
	public function getInputKey(): string;	
	
	/**
	 * Get the title.
	 *
	 * @return string The title
	 */	
	public function getTitle(): string;
	
	/**
	 * Get the description.
	 *
	 * @return string The description
	 */	
	public function getDescription(): string;
	
	/**
	 * Get the area.
	 *
	 * @return string
	 */	
	public function getArea(): string;

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
	): bool;	
}