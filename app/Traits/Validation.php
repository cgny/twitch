<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/30/22
 * Time: 5:24 PM
 */

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Validation{

    private $access_token;

    function validateToken()
    {
        if (isset(Auth::user()->id) && count(Auth::user()->hasAccessToken) == 1)
        {
            $this->access_token = Auth::user()->hasAccessToken[0]->latest_token;
            return true;
        }
        else
        {
            $this->access_token = false;
            return false;
        }
    }

    function getValidatedAccessToken()
    {
        return $this->access_token;
    }

}
