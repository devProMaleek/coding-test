<?php

namespace App\Http\Controllers\Api;

use App\Services\RandomUserService;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\BaseController as BaseController;

class RandomUserController extends BaseController
{
    //
    protected $service;

    public function __construct(RandomUserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = $this->service->getRandomUser();

        if ($user) {
            $userResource = new UserResource($user);
            return $this->sendResponse($userResource, 'Successfully fetched a random user', 200);
        } else {
            return $this->sendError('Something went wrong', ['error' => 'Server Error'], 500);
        }
    }
}
