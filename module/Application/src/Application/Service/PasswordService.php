<?php

namespace Application\Service;

use GoalioForgotPassword\Service\Password;
use SharengoCore\Entity\Customers;

class PasswordService extends Password
{
    /**
     * @param       $password
     * @param       Customers $user
     * @param array $data
     *
     * @return bool
     */
    public function resetPassword($password, $user, array $data)
    {
        $pass = hash("MD5", $data['newCredential']);
        $user->setPassword($pass);
        $user->setRegistrationCompleted(true);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $user]);
        $this->getUserMapper()->update($user);
        $this->remove($password);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, ['user' => $user]);

        return true;
    }
}
