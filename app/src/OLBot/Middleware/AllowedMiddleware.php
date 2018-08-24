<?php

namespace OLBot\Middleware;


use OLBot\Model\DB\AllowedGroup;
use OLBot\Model\DB\AllowedUser;
use OLBot\Service\StorageService;
use Slim\Http\Request;
use Slim\Http\Response;

class AllowedMiddleware
{
    private $storageService;

    function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

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

    private function getUser() {
        return AllowedUser::where(['id' => $this->storageService->message->getFrom()->getId()]);
    }

    private function isBotmaster($id) {
         return $id == $this->storageService->settings['botmaster_id'];

    }
}