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
     * @param  array  $name - код для типа инфоблоков
     * @param $iblockTypeCode
     * @return array|false|void
     */
    public function AddIblockType(array $name, $iblockTypeCode)
    {
        global $DB;

        // проверяем на уникальность
        $db_iblock_type = \CIBlockType::GetList(
            ["SORT" => "ASC"],
            ["ID" => $iblockTypeCode]
        );
        // если его нет - создаём
        if (!$ar_iblock_type = $db_iblock_type->Fetch()) {
            $obBlocktype = new \CIBlockType;
            $DB->StartTransaction();

            // массив полей для нового типа инфоблоков
            $arIBType = [
                'ID'       => $iblockTypeCode,
                'SECTIONS' => 'Y',
                'IN_RSS'   => 'N',
                'SORT'     => 500,
                'LANG'     => [
                    'en' => [
                        'NAME' => $name[ 'en' ][ 'NAME' ],
                    ],
                    'ru' => [
                        'NAME' => $name[ 'ru' ][ 'NAME' ],
                    ]
                ]
            ];

            // создаём новый тип для инфоблоков
            $resIBT = $obBlocktype->Add($arIBType);
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

        return $arIBType;
    }

    /**
     * @param  string  $iblockCode  - символьный код для инфоблока
     * @param  string  $iblockType  - код типа инфоблоков
     * @param  array  $name
     * @return false
     */
    public function AddIblock(string $iblockCode, string $iblockType, array $fields)
    {

        $ib = new \CIBlock;

        // проверка на уникальность
        $resIBE = \CIBlock::GetList(
            [],
            [
                'TYPE' => $iblockType,
                "CODE" => $iblockCode
            ]
        );
        if ($ar_resIBE = $resIBE->Fetch()) {
            return false;
        } else {
            $arFieldsIB = [
                "ACTIVE"         => "Y",
                "NAME"           => $fields['NAME'],
                "CODE"           => $iblockCode,
                "IBLOCK_TYPE_ID" => $iblockType,
                "SITE_ID"        => SITE_ID,
                "GROUP_ID"       => ["2" => "R"],
                "FIELDS"         => [
                    "CODE" => [
                        "IS_REQUIRED"   => "Y",
                        "DEFAULT_VALUE" => [
                            "TRANS_CASE"      => "L",
                            "UNIQUE"          => "Y",
                            "TRANSLITERATION" => "Y",
                            "TRANS_SPACE"     => "-",
                            "TRANS_OTHER"     => "-"
                        ]
                    ]
                ]
            ];
            return $ib->Add($arFieldsIB);
        }
    }
}
