<?php

namespace Idea10\Core\Crm\Component\EntityList;

use Bitrix\Crm\Component\EntityList\NearestActivity\FrontIntegration\FrontIntegration;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;

class Block
{

    private ?array $activity;
    private FrontIntegration $frontIntegration;

    public function __construct(?array $activity, FrontIntegration $frontIntegration)
    {

        $this->activity = $activity;
        $this->frontIntegration = $frontIntegration;
    }

    public function ActivityData(): array
    {
        $deadline = isset($this->activity[ 'DEADLINE' ]) && !\CCrmDateTimeHelper::IsMaxDatabaseDate($this->activity[ 'DEADLINE' ])
            ? DateTime::createFromUserTime($this->activity[ 'DEADLINE' ])->toUserTime()
            : null;

        $timeFormatted = $deadline
            ? \CCrmComponentHelper::TrimDateTimeString(FormatDate('FULL', $deadline))
            : Loc::getMessage('CRM_ACTIVITY_TIME_NOT_SPECIFIED_MSGVER_1')
        ;

        if($this->activity != null){
            $subject = $this->frontIntegration->getSubject($this->activity);
        } else{
            $subject = 'Дела отсутствуют';
        }

        $result = [
            'START_TIME' => htmlspecialcharsbx($timeFormatted),
            'SUBJECT' => htmlspecialcharsbx($subject)
        ];

        return $result;

    }

}
