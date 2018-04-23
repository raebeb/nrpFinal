<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'role_id', 'hospital_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
      return $this->belongsTo(Role::class);
    }

    public function hospital(){
      return $this->belongsTo(Hospital::class);
    }

    public function accessLog(){
      return $this->hasMany(AccessLog::class);
    }

    public function schedule(){
      return $this->hasMany(Schedule::class);
    }

    public function hasRoles(array $roles){
        foreach ($roles as $role) {
          if ($this->role->name === $role) {
            return true;
          }
        }
        return false;
    }

    public function isAdmin(){
      return $this->hasRoles(['admin']);
    }

    public function isBlock(){
      if($this->status){
        return true;
      }
      return false;
    }
    public function findForPassport($identifier) {
      return User::orWhere('email', $identifier)->where('status', 0)->first();
    }

    public function sendPasswordResetNotification($token){
      $this->notify(new ResetPasswordNotification($token));
    }
}
