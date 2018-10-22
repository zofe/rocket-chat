<?php

namespace Noisim\RocketChat\Entities;

use Noisim\RocketChat\Exceptions\ImActionException;
use Noisim\RocketChat\Exceptions\SubscriptionActionException;

class Subscription extends Entity
{
    private $id;

    public function loginByToken($token, $id, $storeInSession = false)
    {
        $this->id = $id;
        $this->authToken = $token;
        $this->add_request_headers([
            'X-Auth-Token' => $token,
            'X-User-Id' => $id,
        ], $storeInSession);
        return $this;
    }

    /* Mark any room (channel, group and DMs) as read. */
    public function read($roomId)
    {
        $response = $this->request()->post($this->api_url("subscriptions.read"))
            ->body(['rid' => $roomId])
            ->send();

        $this->handle_response($response, new SubscriptionActionException());
        return $this;
    }
}