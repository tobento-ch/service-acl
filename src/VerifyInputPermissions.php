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
     * @param AclInterface $acl
     */    
    public function __construct(
        protected AclInterface $acl
    ) {}
    
    /**
     * Verifies input permissions.
     *
     * @param array $permissions The input permissions ['user_create' => 0, 'user_update' => 1]
     * @param RoleInterface $role
     * @return array The verified permissions ['user_create', 'user_update']
     */    
    public function verify(array $permissions, RoleInterface $role): array
    {
        $verified = [];
        $allowedAreaKeys = $role->areas();
        
        foreach($permissions as $key => $value) {
                    
            if ($value != true) {
                continue;
            }
            
            if (!is_string($key)) {
                continue;
            }
            
            // input key to ruleKey
            $key = str_replace(array('_'), array('.'), $key);
                
            if ($this->acl->getRule($key) === null) {
                continue;
            }
            
            $rule = $this->acl->getRule($key);
 
            if (! $rule->requiresPermission()) {
                continue;
            }
            
            if (!in_array($rule->getArea(), $allowedAreaKeys)) {
                continue;
            }
            
            $verified[] = $rule->getKey();
        }

        return array_unique($verified);
    }
}