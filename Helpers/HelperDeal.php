<?php

namespace Idea10\Core\Helpers;

use \Bitrix\Crm\Timeline\CommentEntry;

class HelperDeal
{
    /**
     * @param  int  $DEAL_ID
     * @param  string  $CHECK_PERMISSIONS
     * @param  array  $GET_FIELDS
     * @return array
     */
    protected static function executeDeal(int $DEAL_ID, string $CHECK_PERMISSIONS = "Y", array $GET_FIELDS)
    {
        \Bitrix\Main\Loader::includeModule('crm');
        $result = [];

        $entityResult = \CCrmDeal::GetListEx(
            ['SOURCE_ID' => 'DESC'],
            [
                'ID'                => $DEAL_ID,
                'CHECK_PERMISSIONS' => $CHECK_PERMISSIONS
            ],
            false,
            false,
            $GET_FIELDS
        );

        while ($entity = $entityResult->fetch()) {
            $result = $entity;
        }

        return $result;
    }

    /**
     * @param  int  $deal_id  - ID сделки
     * @param  int  $sender_user_id  - ID пользователя отправителя
     * @param  int  $sender_initiator_user_id  - ID пользователя инициатор отправки
     * @param  string  $text  - ID пользователя
     * @return void
     */
    protected static function executeTimelineDeal(
        int $deal_id,
        int $sender_user_id,
        int $sender_initiator_user_id,
        string $text
    ): void {
        \Bitrix\Main\Loader::includeModule('crm');
        $entryID = \Bitrix\Crm\Timeline\CommentEntry::create(
            [
                'TEXT'      => $text,
                'SETTINGS'  => [
                    'HAS_FILES' => 'N',
                    'AUTHOR_ID' => $sender_initiator_user_id,
                ],
                'AUTHOR_ID' => $sender_user_id,
                'BINDINGS'  => [
                    [
                        'ENTITY_TYPE_ID' => \CCrmOwnerType::Deal,
                        'ENTITY_ID'      => $deal_id
                    ]
                ]
            ]);

        $saveData = [
            'COMMENT'        => $text,
            'ENTITY_TYPE_ID' => \CCrmOwnerType::Deal,
            'ENTITY_ID'      => $deal_id
        ];

        \Bitrix\Crm\Timeline\CommentController::getInstance()->onCreate($entryID, $saveData);
    }

    /**
     * @param  int  $deal_id  - ID сделки
     * @param  int  $user_id  - ID пользователя
     * @param  string  $event_name  - Текст, события
     * @param  string  $before_change  - Текст, до изменений
     * @param  string  $after_change  - Текст, после изменений
     * @return void
     */
    protected static function executeHistoryDeal(
        int $deal_id,
        int $user_id,
        string $event_name,
        string $before_change,
        string $after_change
    ): void {
        \Bitrix\Main\Loader::includeModule('crm');
        $CCrmEvent = new \CCrmEvent();
        $CCrmEvent->Add(
            [
                'ENTITY_TYPE'  => 'DEAL',
                'ENTITY_ID'    => $deal_id,
                'USER_ID'      => $user_id,
                'EVENT_NAME'   => $event_name,
                'EVENT_TEXT_1' => $before_change,
                'EVENT_TEXT_2' => $after_change,
            ]
        );
    }

    /**
     * @param  int  $deal_id  - ID сделки
     * @param  int  $sender_user_id  - ID пользователя отправителя
     * @param  int  $sender_initiator_user_id  - ID пользователя инициатор отправки
     * @param  string  $text  - ID пользователя
     * @return null
     */
    public static function saveInTimelineDeal($deal_id, $sender_user_id, $sender_initiator_user_id, $text)
    {
        return self::executeTimelineDeal($deal_id, $sender_user_id, $sender_initiator_user_id, $text);
    }

    public static function saveInHistoryDeal($deal_id, $user_id, $event_name, $before_change, $after_change)
    {
        return self::executeHistoryDeal($deal_id, $user_id, $event_name, $before_change, $after_change);
    }

    /**
     * @param $DEAL_ID
     * @param $CHECK_PERMISSIONS
     * @param $GET_FIELDS
     * @return array|null
     */
    public static function getDeal($DEAL_ID, $CHECK_PERMISSIONS, $GET_FIELDS): ?array
    {
        return self::executeDeal($DEAL_ID, $CHECK_PERMISSIONS, $GET_FIELDS);
    }
}