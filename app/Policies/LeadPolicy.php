<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\Seller;

class LeadPolicy
{
    // app/Policies/LeadPolicy.php
    public function finish(\App\Models\Seller $user, \App\Models\Lead $lead): bool
    {
        return $user->id === $lead->seller_id && $user->brand_id === $lead->brand_id;
    }
    /**
     * Only Admin OR Front Seller in the same brand may generate payment links.
     */
    public function createPaymentLink($user, Lead $lead): bool
    {
        // Admin guard
        if ($user instanceof Admin) {
            return true;
        }

        // Seller guard (front_seller + same brand)
        if ($user instanceof Seller) {
            $role = $user->role ?? $user->is_seller; // 'front_seller' | 'project_manager'
            return $role === 'front_seller'
                && (int) $user->brand_id === (int) $lead->brand_id;
        }

        return false;
    }


    public function viewPerformance(?object $user, Seller $subject): bool
    {
        // Admin guard?
        if (auth('admin')->check()) {
            return true;
        }

        // Seller guard?
        if (auth('seller')->check()) {
            $viewer = auth('seller')->user();

            // Only allow the same seller to view their own page
            // (blocks PM → Front and Front → PM cross-view)
            return (int)$viewer->id === (int)$subject->id;
        }

        // No guard = no access
        return false;
    }
}
