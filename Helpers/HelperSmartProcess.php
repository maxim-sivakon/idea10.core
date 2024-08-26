<?php

namespace Idea10\Core\Helpers;

use \Bitrix\Main\Type\DateTime;
use \Bitrix\Crm\Service\Container;

class HelperSmartProcess
{
    /**
     * @param  int  $id  - ID элемента
     * @param  int  $id_smart_process  - ID смарт-процесса
     * @return array
     */
    protected static function executeSmartProcess(int $id, int $id_smart_process)
    {
        $factory = \Container::getInstance()->getFactory($id_smart_process);
        $item = $factory->getItem($id);

        return $item->getData();
    }

    /**
     * @param  int  $id  - ID элемента
     * @param  int  $id_smart_process  - ID смарт-процесса
     * @return array
     */
    public static function getSmartProcess($id, $id_smart_process): ?array
    {
        return self::executeSmartProcess($id, $id_smart_process);
    }
}