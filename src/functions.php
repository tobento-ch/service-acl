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

use Psr\Container\ContainerInterface;
use Tobento\Service\HelperFunction\Functions;
use Tobento\Service\Acl\AclInterface;
use Tobento\Service\Acl\Authorizable;

if (!function_exists('acl')) {
    /**
     * Get the acl
     *
     * @return AclInterface
     */
    function acl(): AclInterface
    {
        return Functions::get(ContainerInterface::class)->get(AclInterface::class);
    }
}

if (!function_exists('can')) {
    /**
     * Helper function $acl->can().
     *
     * @param string $key A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array $parameters Any parameters for custom handler
     * @param null|Authorizable $user If null current user is taken.
     * @return bool True on success, false on failure.
     */
    function can(string $key, array $parameters = [], ?Authorizable $user = null): bool
    {
        $acl = Functions::get(ContainerInterface::class)->get(AclInterface::class);
        return $acl->can($key, $parameters, $user);
    }
}

if (!function_exists('cant')) {
    /**
     * Helper function $acl->cant().
     *
     * @param string $key A permission key 'user.create' or multiple keys 'user.create|user.update'
     * @param array $parameters Any parameters for custom handler
     * @param null|Authorizable $user If null current user is taken.
     * @return bool True on success, false on failure.
     */
    function cant(string $key, array $parameters = [], ?Authorizable $user = null): bool
    {
        $acl = Functions::get(ContainerInterface::class)->get(AclInterface::class);
        return $acl->cant($key, $parameters, $user);
    }
}