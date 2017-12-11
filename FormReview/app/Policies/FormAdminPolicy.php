<?php

namespace App\Policies;

use App\Form;
use App\FormAdmin;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormAdminPolicy
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

    public function update(User $user, FormAdmin $formAdmin)
    {
        return $user->id == $formAdmin->inviter_id;
    }
    public function destroy(User $user, FormAdmin $formAdmin)
    {
        return $user->id == $formAdmin->inviter_id;
    }
    public function handle(User $user, FormAdmin $formAdmin)
    {
        return $user->id == $formAdmin->user_id;
    }
}
