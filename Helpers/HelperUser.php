<?php

namespace Tknovosib\Helpers;

class HelperUser
{

    /**
     * @param $USER_ID  - ID пользователя
     * @return array|null
     */
    protected static function executeUser($USER_ID): ?array
    {
        $arUser = \CUser::GetByID($USER_ID)->Fetch();
        $arUser[ "FIRST_LAST_NAME" ] = $arUser[ "NAME" ].' '.$arUser[ "LAST_NAME" ];

        return $arUser;
    }

    /**
     * @param $USER_ID  - ID пользователя
     * @return array|null
     */
    public static function getUser($USER_ID): ?array
    {
        return self::executeUser($USER_ID);
    }

    /**
     * Sends instant message.
     *
     * @param array $arFields
     * <pre>
     * [
     * 	(string) MESSAGE_TYPE - Message type:
     * 		use const IM_MESSAGE_SYSTEM = S - notification,
     * 		use const IM_MESSAGE_PRIVATE = P - private chat,
     * 		use const IM_MESSAGE_CHAT = S - group chat,
     * 		use const IM_MESSAGE_OPEN = O - open chat,
     * 		use const IM_MESSAGE_OPEN_LINE = L - open line chat.
     *
     * 	(string|int) DIALOG_ID - Dialog Id:
     * 		chatNNN - chat,
     * 		sgNNN - sonet group,
     * 		crmNNN - crm chat,
     * 		NNN - recipient user.
     *
     * 	(string) MESSAGE_DATE - Setup the message creation date. String representation of datetime.
     * 	(int) TO_CHAT_ID - Chat Id.
     * 	(int) TO_USER_ID - Recipient user Id.
     * 	(int) FROM_USER_ID - Sender user Id.
     * 	(int) AUTHOR_ID - Created by Id.
     * 	(string) MESSAGE - Message to send.
     * 	(string) MESSAGE_OUT - Formated rich message.
     * 	(Y|N) SYSTEM - Display message as a system notification.
     *
     * 	(array | \CIMMessageParamAttach) ATTACH - Message attachment.
     * 	(array) FILES - Message file attachments.
     * 	(\Bitrix\Im\Bot\Keyboard) KEYBOARD - Message keyboard.
     * 	(\Bitrix\Im\Bot\ContextMenu) MENU - Message context menu.
     * 	(array) PARAMS - Message additional parameters.
     *
     * 	(int) NOTIFY_TYPE - Notification type:
     * 		use const IM_NOTIFY_CONFIRM = 1 - confirm,
     * 		use const IM_NOTIFY_FROM = 2 - notify single from,
     * 		use const IM_NOTIFY_SYSTEM = 4 - notify single.
     * 	(string) NOTIFY_MODULE - Source module id (ex: xmpp, main, etc).
     * 	(string) NOTIFY_EVENT - Source module event id for search (ex: IM_GROUP_INVITE).
     * 	(string) NOTIFY_TITLE - Notify title for sending email.
     * 	(string) TITLE - Alias for NOTIFY_TITLE parameter.
     * 	(array) NOTIFY_BUTTONS - Array of buttons - available with NOTIFY_TYPE = 1
     * 					Array(
     * 						Array('TITLE' => 'OK', 'VALUE' => 'Y', 'TYPE' => 'accept', 'URL' => '/test.php?CONFIRM=Y'),
     * 						Array('TITLE' => 'Cancel', 'VALUE' => 'N', 'TYPE' => 'cancel', 'URL' => '/test.php?CONFIRM=N'),
     * 					)
     * 	(string) NOTIFY_TAG - Field for group in JS notification and search in table.
     * 	(string) NOTIFY_SUB_TAG - Second TAG for search in table.
     * 	(Y|N) NOTIFY_ANSWER - Allow answering right in notification balloon.
     * 	(string) EMAIL_TEMPLATE - E-mail template code.
     * 	(string) NOTIFY_EMAIL_TEMPLATE - Alias to EMAIL_TEMPLATE parameter.
     * 	(string) NOTIFY_LINK - Url to dislplay in notification balloon.
     * 	(string) NOTIFY_MESSAGE - Alias for MESSAGE parameter.
     * 	(string) NOTIFY_MESSAGE_OUT - Alias for MESSAGE_OUT parameter.
     * 	(string) NOTIFY_ONLY_FLASH - Display only balloon without adding message into notification list.
     *
     * 	(Y|N) PUSH - Allows sending pull.
     * 	(string) MESSAGE_PUSH - Private or chat push message.
     * 	(string) PUSH_MESSAGE - Notification push message.
     * 	(array) PUSH_PARAMS - Notification push params.
     * 	(string) PUSH_IMPORTANT - Send push immediately.
     * 	(string) TEMPLATE_ID - UUID of the message, which generates on the frontend.
     * 	(string) FILE_TEMPLATE_ID
     * 	(array) EXTRA_PARAMS
     *
     * 	(bool) RECENT_SKIP_AUTHOR - Do not add author in recent list in case of self message chat.
     * 	(Y|N) RECENT_ADD - N = Skip refresh of the recent list for chat relations. Do not flow up recent on hidden notification.
     *
     * 	(int) IMPORT_ID - The ID of the message to be imported.
     * 	(Y|N) CONVERT - Suppress events firing and pull sending on import operations.
     *
     * 	(Y|N) URL_PREVIEW - Generate URL preview attachment and insert date PUT/SEND command: Y|N.
     * 	(Y|N) SKIP_URL_INDEX - Skip Link\Url processing @see \Bitrix\Im\V2\Link\Url\UrlService::saveUrlsFromMessage
     * 	(Y|N) SKIP_COUNTER_INCREMENTS - Skip increasing counters
     * 	(Y|N) SKIP_PULL - Skip send pull
     *
     * 	(Y|N) SKIP_COMMAND - Skip command execution @see \Bitrix\Im\Command::onCommandAdd
     *
     * 	(Y|N) SILENT_CONNECTOR - Keep silent. Do not send message into OL connector to client side. @see \Bitrix\ImOpenLines\Connector::onMessageSend
     * 	(Y|N) SKIP_CONNECTOR - Do not send message into OL connector to client side. @see \Bitrix\ImOpenLines\Connector::onMessageSend
     * 	(Y|N) IMPORTANT_CONNECTOR - Forward message into OL channel either mark as system. @see \Bitrix\ImOpenLines\Connector::onMessageSend
     * 	(Y|N) NO_SESSION_OL - Do not touch OL session @see \Bitrix\ImOpenLines\Connector::onMessageSend
     *
     * 	(Y|N) SKIP_USER_CHECK - Skip chat relations check. Check if user has permission to write into open chat, open line or announce channel. Default - N
     * ]
     * </pre>
     *
     * @return int|false
     */
    public static function sendMessageUserB24($toUserID, $fromUserID, $message, $titleMsg): void {
        \Bitrix\Main\Loader::includeModule('im');
        $chatID = \CIMMessage::GetChatId($toUserID, $fromUserID, true);

        $arFields = [
            'MESSAGE_TYPE' => IM_MESSAGE_OPEN,
            'DIALOG_ID' => $chatID,
            'MESSAGE_DATE' => new \Bitrix\Main\Type\DateTime(),
            'TO_CHAT_ID' => $chatID,
            'TO_USER_ID' => $toUserID,
            'FROM_USER_ID' => $fromUserID,
            'AUTHOR_ID' => $fromUserID,
            'MESSAGE' => $message,
            'SYSTEM' => 'N',
            'NOTIFY_TYPE' => 1,
            'NOTIFY_MODULE' => 'im',
            'NOTIFY_TITLE' => $titleMsg,
            'TITLE' => $titleMsg,
            'PUSH' => 'Y',
        ];

        \CIMMessenger::Add($arFields);
    }


}