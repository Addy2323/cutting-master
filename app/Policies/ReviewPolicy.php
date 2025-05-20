<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->hasRole('admin');
    }
} 