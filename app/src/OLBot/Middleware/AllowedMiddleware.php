<?php

namespace OLBot\Middleware;


use OLBot\Model\DB\AllowedGroup;
use OLBot\Model\DB\AllowedUser;
use Slim\Http\Request;
use Slim\Http\Response;

class AllowedMiddleware extends TextBasedMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $id = $this->storageService->message->getChat()->getId();

        if ($this->isAllowedUser($id) || $this->isAllowedGroup($id)) {
            $this->storageService->user = $this->getUser();
            if ($this->isBotmaster($id)) {
                $this->storageService->botmaster = true;
            }
            return $next($request, $response);
        } else {
            return $response->withStatus(403);
        }
    }

    private function isAllowedUser($id)
    {
        return $id > 0 && AllowedUser::where(['id' => $id, 'active' => true])->count();
    }

    private function isAllowedGroup($id)
    {
        return $id < 0 && AllowedGroup::where(['id' => $id, 'active' => true])->count();
    }

    private function getUser()
    {
        //TODO: register (inactive?) User if unknown
        return AllowedUser::find($this->storageService->message->getFrom()->getId());
    }

    private function isBotmaster($id)
    {
         return $id == $this->storageService->settings->getBotmasterId();
    }
}