<?php

namespace App\Policies;

use App\User;
use App\Schedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class HistoryCsvPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    public function before($user, $ability, Schedule $schedule){
      if ($user->hasRoles(['moderador','admin']) && $user->hospital_id === $schedule->user->hospital_id) {
        return true;
      }
    }

    public function edit(User $user, Schedule $schedule){
      return $user->id === $schedule->user_id;
    }
}
