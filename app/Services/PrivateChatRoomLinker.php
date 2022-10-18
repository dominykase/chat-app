<?php

namespace App\Services;

use App\Models\User;

class PrivateChatRoomLinker
{
    public function link($userId, $roomId) : void
    {
        $privateRoomIds = User::where('id', $userId)
            ->pluck('private_room_ids')[0];

        $this->stripNull($privateRoomIds);

        if (!in_array($roomId, explode(",", $privateRoomIds)))
        {
            User::where('id', $userId)
                ->update([
                    'private_room_ids' => $privateRoomIds . $roomId . ","
                ]);
        }
    }

    public function hasUser($user, $chatRoom) : bool
    {
        $privateRoomIds = $user->private_room_ids;

        if ($privateRoomIds == null)
        {
            return false;
        }

        $idsArray = explode(",", $privateRoomIds);

        foreach ($idsArray as $id)
        {

            if ($id == $chatRoom->id)
            {
                return true;
            }
        }

        return false;
    }

    private function stripNull(&$privateRoomIds)
    {
        if ($privateRoomIds == "null") {
            $privateRoomIds = "";
        }
    }
}
