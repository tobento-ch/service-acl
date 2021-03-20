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
 * Verifies the input permissions
 */
class VerifyInputPermissions
{
    /**
     * Create a new VerifyInputPermissions
     *
     * @param AclInterface
     */    
    public function __construct(
        protected AclInterface $acl,
    ) {}
    
    /**
     * Verifies input permissions.
     *
     * @param array The input permissions ['user_create' => 0, 'user_update' => 1]
     * @param RoleInterface
     * @return The verified permissions ['user_create', 'user_update']
     */    
    public function verify(array $permissions, RoleInterface $role): array
    {
        $verified = [];
        $allowedAreaKeys = $role->areas();
        
        foreach($permissions as $key => $value) {
            // input key to ruleKey
            $key = str_replace(array('_'), array('.'), $key);
            
            if ($value !== '1') {
                continue;
            }
            
            if (!is_string($key)) {
                continue;
            }
                
            if ($this->acl->getRule($key) === null) {
                continue;
            }
            
            $rule = $this->acl->getRule($key);
        
            if (!in_array($rule->getArea(), $allowedAreaKeys)) {
                continue;
            }
            
            $verified[] = $rule->getKey();
        }

        return array_unique($verified);
    }
}