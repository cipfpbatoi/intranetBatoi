<?php

namespace Intranet\Handlers;

use Illuminate\Contracts\Auth\Access\Gate as Gate;
use Illuminate\Contracts\Auth\Guard as Auth;

/**
 * Controlador d'accés per a menús/camps legacy.
 *
 * Es manté com a classe d'adaptació interna perquè el codi d'aplicació no
 * depenga de tipus de `styde/html`.
 */
class MyAccessHandler
{

    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function setGate(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Check if the user has access to an item based on the passed $options.
     *
     * The method will search for one of these options, in the following order,
     * and only one option will be used to check if the user has access:
     *
     * 1. callback (should return true if access is granted, false otherwise)
     * 2. logged (true: requires authenticated user, false: requires guest user)
     * 3. roles (true if the user has any of the required roles)
     * 4. Returns true if no security options are set.
     *
     * @param array $options
     * @return bool
     */
    public function check(array $options)
    {
        if (isset($options['callback'])) {
            return call_user_func($options['callback'], $options);
        }

        if (isset($options['logged'])) {
            return $options['logged'] === $this->auth->check();
        }

        if (isset($options['roles'])) {
            return $this->checkRole($options['roles']);
        }

        if (isset($options['allows'])) {
            return $this->checkGate($options['allows']);
        }

        if (isset($options['check'])) {
            return $this->checkGate($options['check']);
        }

        if (isset($options['denies'])) {
            return !$this->checkGate($options['denies']);
        }

        return true;
    }

    protected function checkGate($arguments)
    {
        if ($this->gate == null) {
            throw new \RuntimeException(
            'You have to upgrade to Laravel 5.1.12 or superior'
            . ' to use the allows, checks or denies options'
            );
        }

        if (is_array($arguments)) {
            $ability = array_shift($arguments);
        } else {
            $ability = $arguments;
            $arguments = array();
        }

        return call_user_func_array([$this->gate, 'check'], [$ability, $arguments]);
    }

    /**
     * Check if a user has at least one of the allowed roles.
     *
     * @param  $allowed
     * @return bool
     */
    protected function checkRole($allowed)
    {
        if (isAdmin()) return true;
        if (!is_array($allowed)) {
            $allowed = explode('|', $allowed);
        }
        $allow = false;
        foreach ($allowed as $role) {
            if (userIsAllow($role))
                $allow = true;
        }
        return $allow;
    }

}
