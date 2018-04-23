<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before($user, $ability, User $editUser){
      if($user->isAdmin() && ($user->hospital->id === $editUser->hospital->id)){
        return true;
      }
    }

    public function edit(User $authUser, User $user){
      return $authUser->id === $user->id;
    }

    public function update(User $authUser, User $user){
      return $authUser->id === $user->id;
    }
}
