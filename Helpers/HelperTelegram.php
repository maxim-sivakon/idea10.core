<?php

namespace Idea10\Core\Helpers;

use \Bitrix\Main\SystemException;
use \Bitrix\Main\Web\HttpClient;

class HelperTelegram
{

    /**
     * @param $token  - токен бота телеграм
     * @param $params  - параметры
     * @return mixed
     * @throws SystemException
     */
    protected static function executeRequestToTelegram($token, $params)
    {
        $baseUrl = 'https://api.telegram.org/bot'.$token.'/sendMessage';
        $httpClient = new HttpClient([
            'socketTimeout'          => 20,
            'streamTimeout'          => 60,
            'waitResponse'           => true,
            'disableSslVerification' => true,
        ]);

        $request = $httpClient->post($baseUrl, $params);
    }

    /**
     * Перед тем как отправлять ЛС пользователю - нужно, чтобы пользователь зарегистрировался в боте. Только тогда сможет получать сообщения.
     *
     * @param $token  - токен бота телеграм
     * @param $chat_id  - ID пользователя
     * @param $text  - отправляемый текст
     * @param $parse_mode  - тип обработки отправляемого текста, например html, markdown
     * @return mixed
     * @throws SystemException
     */
    public static function sendPrivateMessage($token, $chat_id, $text, $parse_mode)
    {
        return self::executeRequestToTelegram($token, [
            "chat_id"    => $chat_id,
            "text"       => $text,
            "parse_mode" => $parse_mode
        ]);
    }

    /**
     * @param $token  - токен бота телеграм
     * @param $chat_id  - ID группы
     * @param $parse_mode  - тип обработки отправляемого текста, например html, markdown
     * @param $text  - отправляемый текст
     * @return mixed
     * @throws SystemException
     */
    public static function sendToGroup($token, $chat_id, $parse_mode, $text)
    {
        return self::executeRequestToTelegram($token, [
            "chat_id"    => $chat_id,
            "text"       => $text,
            "parse_mode" => $parse_mode
        ]);
    }

    /**
     * @param $token  - токен бота телеграм
     * @param $chat_id  - ID мульти-группы
     * @param $parse_mode  - тип обработки отправляемого текста, например html, markdown
     * @param $text  - отправляемый текст
     * @param $message_thread_id  - ID папки мульти-группы
     * @return mixed
     * @throws SystemException
     */
    public static function sendToMultiGroup($token, $chat_id, $parse_mode, $message_thread_id, $text)
    {
        return self::executeRequestToTelegram($token, [
            "chat_id"           => $chat_id,
            "message_thread_id" => $message_thread_id,
            "text"              => $text,
            "parse_mode"        => $parse_mode
        ]);
    }

}