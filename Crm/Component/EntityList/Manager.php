<?php

namespace Idea10\Core\Crm\Component\EntityList;

use Bitrix\Crm\Activity\Entity\EntityUncompletedActivityTable;
use Bitrix\Crm\Component\EntityList\NearestActivity\FrontIntegration\FrontIntegration;
use Bitrix\Crm\Service\Container;

class Manager
{

    private int $entityTypeId;

    public function __construct(int $entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
    }

    public function appendNearestActivityBlock(
        array $items,
        bool $isItemListMode = false
    ): array
    {
        if (empty($items))
        {
            return $items;
        }
        $ids = array_column($items, 'ID');
        $activitiesData = $this->getActivitiesData($ids);
        $entitiesWithoutActivities = array_diff($ids, array_keys($activitiesData));
        $waitsData = $this->getWaitData($entitiesWithoutActivities);

        foreach ($items as $k => $item)
        {
            $entityId = (int)$item['ID'];
            $allowEdit = $item['EDIT'] ?? false;

            $block = new \Tknovosib\Crm\Component\EntityList\Block(
                $activitiesData[$entityId] ?? null,
                FrontIntegration::make($isItemListMode, $allowEdit)
            );

            if (!isset($activitiesData[$entityId]))
            {
                $waitText = $waitsData[$entityId] ?? null;
                if ($waitText)
                {
                    $block->setEmptyStatePlaceholder($waitText);
                }
            }
            $items[$k]['ACTIVITY_BLOCK'] = $block;
        }

        return $items;
    }

    private function getActivitiesData(array $entityIds): array
    {
        $activitiesIds = $this->getNearestActivitiesIds($entityIds);
        if (empty($activitiesIds))
        {
            return $activitiesIds;
        }
        $result = [];

        $activitiesIterator = \CCrmActivity::GetList([], ['ID' => array_keys($activitiesIds)], false, false, [
            'ID',
            'TYPE_ID',
            'PROVIDER_ID',
            'SUBJECT',
            'RESPONSIBLE_ID',
            'DEADLINE',
            'ORIGIN_ID',
            'IS_INCOMING_CHANNEL',
            'LIGHT_COUNTER_AT',
        ]);
        while ($activity = $activitiesIterator->Fetch())
        {
            $entityId = $activitiesIds[$activity['ID']];
            $result[$entityId] = $activity;
        }

        return $result;
    }

    private function getNearestActivitiesIds(array $entityIds): array
    {
        if (empty($entityIds))
        {
            return [];
        }

            $allNearestActivitiesIterator = EntityUncompletedActivityTable::query()
                ->where('ENTITY_TYPE_ID', $this->entityTypeId)
                ->whereIn('ENTITY_ID', $entityIds)
                ->where('RESPONSIBLE_ID', 0) // 0 means all users
                ->setSelect(['ACTIVITY_ID', 'ENTITY_ID'])
                ->exec()
            ;
            while ($aNearestActivity = $allNearestActivitiesIterator->fetch())
            {
                $entityActivities[$aNearestActivity['ACTIVITY_ID']] = $aNearestActivity['ENTITY_ID'];
            }

        return $entityActivities;
    }

    private function getWaitData(array $entityIds): array
    {
        if (!in_array($this->entityTypeId, [\CCrmOwnerType::Lead, \CCrmOwnerType::Deal, \CCrmOwnerType::Order]))
        {
            return [];
        }

        if (empty($entityIds))
        {
            return [];
        }

        $result = [];

        $waitingInfos = \Bitrix\Crm\Pseudoactivity\WaitEntry::getRecentInfos($this->entityTypeId, $entityIds);
        foreach($waitingInfos as $waitingInfo)
        {
            $entityID = (int)$waitingInfo['OWNER_ID'];
            $result[$entityID] = $waitingInfo['TITLE'];
        }

        return $result;
    }
}
