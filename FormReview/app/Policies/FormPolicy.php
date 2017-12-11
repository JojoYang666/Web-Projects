<?php

namespace App\Policies;

use App\Form;
use App\FormAdmin;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
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

    /*public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }*/

    private function auth(User $user, Form $form, $auth)
    {
        if ($user->id == $form->creator) {
            return true;
        }
        $formAdmin = FormAdmin::where('user_id', $user->id)->where('form_id', $form->id)->accepted()->first();
        if ($formAdmin && in_array($auth, json_decode($formAdmin->authorities))) {
            return true;
        }
        return false;
    }

    /**
     * 判断给定文章是否可以被给定用户更新
     *
     * @param User $user
     * @param Form $form
     * @return bool
     */
    public function update(User $user, Form $form)
    {
        return $this->auth($user, $form, 'update');
    }

    public function show(User $user, Form $form)
    {
        return $this->auth($user, $form, 'show');
    }

    public function destroy(User $user, Form $form)
    {
        return $this->auth($user, $form, 'destroy');
    }

    public function datalist(User $user, Form $form)
    {
        return $this->auth($user, $form, 'datalist');
    }

    public function report(User $user, Form $form)
    {
        return $this->auth($user, $form, 'report');
    }

    public function creator(User $user, Form $form)
    {
        return $user->id == $form->creator;
    }

    public function review(User $user, Form $form)
    {
        return $this->auth($user, $form, 'review');
    }

    public function remark(User $user, Form $form)
    {
        return $this->auth($user, $form, 'remark');
    }
}
