<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2018/11/28
 * Time: 22:39
 */

namespace App\Controllers;

use App\Entity\User;
use App\Models\Form\Auth;

use Rid\Http\Controller;


class AuthController extends Controller
{

    public function actionRegister()
    {
        if (app()->request->isPost()) {
            $register_form = new Auth\UserRegisterForm();
            $register_form->setInput(app()->request->post());
            $success = $register_form->validate();
            if (!$success) {
                return $this->render('action/fail', [
                    'title' => 'Register Failed',
                    'msg' => $register_form->getError()
                ]);
            } else {
                $register_form->flush();  // Save this user in our database and do clean work~

                if ($register_form->getStatus() == User::STATUS_CONFIRMED) {
                    return app()->response->redirect('/index');
                } else {
                    return $this->render('auth/register_pending', [
                        'confirm_way' => $register_form->getConfirmWay(),
                        'email' => $register_form->email
                    ]);
                }
            }
        } else {
            return $this->render('auth/register');
        }
    }

    public function actionConfirm()
    {
        $confirm = new Auth\UserConfirmForm();
        $confirm->setInput(app()->request->get());
        $success = $confirm->validate();
        if (!$success) {
            return $this->render('action/fail', [
                'title' => 'Confirm Failed',
                'msg' => $confirm->getError()
            ]);
        } else {
            $confirm->flush();
            return $this->render('action/success', [
                'notice' => $confirm->getConfirmMsg(),
                'redirect' => '/auth/login'
            ]);
        }
    }

    public function actionRecover()
    {
        if (app()->request->isPost()) {
            $form = new Auth\UserRecoverForm();
            $form->setInput(app()->request->post());
            $success = $form->validate();
            if (!$success) {
                return $this->render('action/fail', [
                    'title' => 'Action Failed',
                    'msg' => $form->getError()
                ]);
            } else {
                $flush = $form->flush();
                if ($flush === true) {
                    return $this->render('auth/recover_next_step');
                } else {
                    return $this->render('action/fail', [
                        'title' => 'Confirm Failed',
                        'msg' => $flush
                    ]);
                }
            }
        } else {
            return $this->render('auth/recover');
        }
    }

    /** @noinspection PhpUnused */
    public function actionLogin()
    {
        $render_data = [];

        if (app()->request->isPost()) {
            $login = new Auth\UserLoginForm();
            if (false === $success = $login->validate()) {
                $login->loginFail();
                $render_data['error_msg'] =  $login->getError();
            } else {
                $login->flush();

                $return_to = app()->session->pop('login_return_to') ?? '/index';
                return app()->response->redirect($return_to);
            }
        }

        $render_data['test_attempts'] = app()->redis->hGet('Site:fail_login_ip_count', app()->request->getClientIp()) ?: 0;
        return $this->render('auth/login', $render_data);
    }

    /** @noinspection PhpUnused */
    public function actionLogout()
    {
        $logout = new Auth\UserLogoutForm();
        if (false === $logout->validate()) {
            return $this->render('action/fail', ['msg' => $logout->getError()]);
        } else {
            $logout->flush();
        }

        return app()->response->redirect('/auth/login');
    }
}
