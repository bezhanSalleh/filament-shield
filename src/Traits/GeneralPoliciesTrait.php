<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

trait GeneralPoliciesTrait
{
    /**
     * Get the permission suffix for the model.
     * Can be overridden in the class using this trait.
     *
     * @return string
     */
    protected function getPermissionSuffix(): string
    {

        return '';
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny' . $this->getPermissionSuffix());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function view(User $user, Model $model): bool
    {
        return $user->can('view' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create' . $this->getPermissionSuffix());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function update(User $user, Model $model): bool
    {
        return $user->can('update' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->can('delete' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function restore(User $user, Model $model): bool
    {
        return $user->can('restore' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can('forceDelete' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can delete any models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('deleteAny' . $this->getPermissionSuffix());
    }

    /**
     * Determine whether the user can permanently delete any models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('forceDeleteAny' . $this->getPermissionSuffix());
    }

    /**
     * Determine whether the user can restore any models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restoreAny' . $this->getPermissionSuffix());
    }

    /**
     * Determine whether the user can replicate the model.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function replicate(User $user, Model $model): bool
    {
        return $user->can('replicate' . $this->getPermissionSuffix(), $model);
    }

    /**
     * Determine whether the user can reorder models.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder' . $this->getPermissionSuffix());
    }
}
