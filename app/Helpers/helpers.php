<?php

if(!function_exists('getSessionUser')) {

  function getSessionUser() {
    
    $sessionUser = request()->server('HTTP_SESSION_USER');

    return $sessionUser;

  }

}
