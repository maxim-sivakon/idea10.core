<?php

namespace Idea10\Core\Helpers;

use \Bitrix\Main\Loader;

class HelperIBlock
{

    private $_aIblocksId = [];

    public function __construct()
    {
        if (!Loader::includeModule("iblock")) {
            throw new Exception("Не найден модуль инфоблоков");
        }
        $this->_getAllIblockId();
    }

    /**
     * возвращает список всех ИБ системы
     *
     * @throws Exception
     */
    public function getAll()
    {
        $aFilter = ["CHECK_PERMISSIONS" => "N"];
        $aResult = $this->_getList([], $aFilter);
        return $aResult;
    }

    private function _getList($aOrder = [], $aFilter = [])
    {
        $aResult = [];
        $oDbRes = \CIBlock::GetList($aOrder, $aFilter, false);
        while ($aDbRes = $oDbRes->fetch()) {
            $oProperty = \CIBlockProperty::GetList([], ["ACTIVE" => "Y", "IBLOCK_ID" => $aDbRes[ "ID" ]]);
            while ($aFields = $oProperty->GetNext()) {
                $aDbRes[ "PROPERTY" ][ $aFields[ "CODE" ] ] = $aFields;
            }
            $aResult[ $aDbRes[ "CODE" ] ] = $aDbRes;
        }
        return $aResult;
    }

    private function _getAllIblockId()
    {
        $aFilter = ["CHECK_PERMISSIONS" => "N", "ACTIVE" => "Y"];
        $aResult = $this->_getList([], $aFilter);
        foreach ($aResult as $aItem) {
            $this->_aIblocksId[ $aItem[ "CODE" ] ] = $aItem[ "ID" ];
        }
    }

    public function getIdByCode($iIblockCode)
    {
        $iIblockId = 0;
        if (isset($this->_aIblocksId[ $iIblockCode ]) && $this->_aIblocksId[ $iIblockCode ] != "") {
            $iIblockId = $this->_aIblocksId[ $iIblockCode ];
        } else {
            $this->_getAllIblockId();
            if (isset($this->_aIblocksId[ $iIblockCode ]) && $this->_aIblocksId[ $iIblockCode ] != "") {
                $iIblockId = $this->_aIblocksId[ $iIblockCode ];
            }
        }
        return $iIblockId;
    }

    public function getCodeById($iElementId)
    {
        $aFilter = ["CHECK_PERMISSIONS" => "N", "ACTIVE" => "Y", "ID" => $iElementId];
        return $this->_getList([], $aFilter);
    }

    /**
     * @param  array  $aFields
     * @return array|false|void
     */
    public function AddIblockType(array $aFields)
    {
        global $DB;

        // проверяем на уникальность
        $db_iblock_type = \CIBlockType::GetList(
            ["SORT" => "ASC"],
            ["ID" => $aFields['ID']]
        );
        // если его нет - создаём
        if (!$ar_iblock_type = $db_iblock_type->Fetch()) {
            $obBlocktype = new \CIBlockType;
            $DB->StartTransaction();

            // создаём новый тип для инфоблоков
            $resIBT = $obBlocktype->Add($aFields);
            if (!$resIBT) {
                $DB->Rollback();
                echo 'Error: '.$obBlocktype->LAST_ERROR;
                die();
            } else {
                $DB->Commit();
            }
        } else {
            return false;
        }

        return $aFields;
    }

    /**
     * @param  array  $aFields
     * @return false
     */
    public function AddIblock(array $aFields)
    {
        $ib = new \CIBlock;

        // проверка на уникальность
        $resIBE = \CIBlock::GetList(
            [],
            [
                'TYPE' => $aFields['IBLOCK_TYPE_ID'],
                "CODE" => $aFields['CODE']
            ]
        );
        if ($ar_resIBE = $resIBE->Fetch()) {
            return false;
        } else {
            return $ib->Add($aFields);
        }
    }
}
