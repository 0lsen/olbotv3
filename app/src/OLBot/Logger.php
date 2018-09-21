<?php

namespace OLBot;


use OLBot\Model\DB\LogError;
use OLBot\Model\DB\LogMessageIn;
use OLBot\Model\DB\LogMessageOut;
use Swagger\Client\ObjectSerializer;
use Swagger\Client\Telegram\Message;
use Swagger\Client\Telegram\SendMessageBody;
use Swagger\Client\Telegram\SendPhotoLinkBody;

class Logger
{
    public static function logError($idIn, \Throwable $t)
    {
        LogError::create([
            'id_in' => $idIn,
            'message' => $t->getCode() . ' - ' . $t->getMessage() . ' [' . $t->getFile() . '::' . $t->getLine() . '] \n' . $t->getTraceAsString()
        ]);
    }

    public static function logMessageIn(Message $message)
    {
        LogMessageIn::create([
            'id_in' => $message->getMessageId(),
            'content' => json_encode(ObjectSerializer::sanitizeForSerialization($message))
        ]);
    }

    /**
     * @param SendMessageBody|SendPhotoLinkBody $message
     */
    public static function logMessageOut($message)
    {
        LogMessageOut::create([
            'id_in' => $message->getReplyToMessageId(),
            'content' => json_encode(ObjectSerializer::sanitizeForSerialization($message))
        ]);
    }
}