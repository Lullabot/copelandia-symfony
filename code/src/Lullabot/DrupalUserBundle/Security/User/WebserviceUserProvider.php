<?php

namespace Lullabot\DrupalUserBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        $userData = '...';
        // pretend it returns an array on success, false if there is no user        $user = array();
        if ($userData) {
            $password = 'test';
            $salt = '';
            $roles = array('ROLE_EDITOR');

            // ...

            return new WebserviceUser($username, $password, $salt, $roles);
        }
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Lullabot\DrupalUserBundle\Security\User\WebserviceUser';
    }
}
