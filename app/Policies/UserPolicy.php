<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        // FIXME: should use role
        return $user->email === env('ADMIN_EMAIL');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function view(User $user, User $model)
    {
        // FIXME: should use role
        return $user->email === env('ADMIN_EMAIL') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        // FIXME: should use role
        return $user->email === env('ADMIN_EMAIL') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        // FIXME: should use role
        return $user->email === env('ADMIN_EMAIL');
    }
}
