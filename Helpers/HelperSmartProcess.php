<?php

namespace Core\Helpers;

use \Bitrix\Main\Type\DateTime;
use \Bitrix\Crm\Service\Container;

class HelperSmartProcess
{
    /**
     * @param  int  $id  - ID элемента
     * @param  int  $idSmartProcess  - ID смарт-процесса
     * @return array
     */
    protected static function executeSmartProcess(int $id, int $idSmartProcess)
    {
        $factory = Container::getInstance()->getFactory($idSmartProcess);
        $item = $factory->getItem($id);

        return $item->getData();
    }

    /**
     * @param  int  $id  - ID элемента
     * @param  int  $idSmartProcess  - ID смарт-процесса
     * @return array
     */
    public static function getSmartProcess($id, $idSmartProcess): ?array
    {
        return self::executeSmartProcess($id, $idSmartProcess);
    }
}