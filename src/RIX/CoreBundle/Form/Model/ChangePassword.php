<?php

namespace RIX\CoreBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     */
    protected $oldPassword;

    /**
     * @Assert\Regex(
     *     pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/",
     *     message="Your password should contain at least 1 lower case letter, 1 upper case letter and 1 number"
     * )
     * @Assert\Length(
     *     min = 6,
     *     max = 15,
     *     minMessage = "Your password must be at least {{ limit }} characters long",
     *     maxMessage= "Your password cannot be longer then {{ limit }} characters"
     * )
     */
    protected $newPassword;

    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }
}