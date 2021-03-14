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
 * RoleInterface
 */
interface RoleInterface extends Permissionable
{
    /**
     * Get the id
     *
     * @return string
     */
    public function id(): int;
    
    /**
     * Get the key
     *
     * @return string
     */
    public function key(): string;

    /**
     * If the role is active
     *
     * @return bool
     */
    public function active(): bool;

    /**
     * Get the area
     *
     * @return string
     */
    public function area(): string;

    /**
     * Get the name
     *
     * @return string
     */
    public function name(): string;
}