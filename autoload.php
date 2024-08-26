<?php
function autoLoadClassesForModules(?string $module_id, string $namespace)
{
    return [
        $namespace.'\Core\Helpers\HelperDeal'               => 'lib/Core/Helpers/HelperUser.php',
        $namespace.'\Core\Helpers\HelperHL'                 => 'lib/Core/Helpers/HelperHL.php',
        $namespace.'\Core\Helpers\HelperHtmlModuleSettings' => 'lib/Core/Helpers/HelperHtmlModuleSettings.php',
        $namespace.'\Core\Helpers\HelperIBlock'             => 'lib/Core/Helpers/HelperIBlock.php',
        $namespace.'\Core\Helpers\HelperIBlockProperty'     => 'lib/Core/Helpers/HelperIBlockProperty.php',
        $namespace.'\Core\Helpers\HelperSale'               => 'lib/Core/Helpers/HelperSale.php',
        $namespace.'\Core\Helpers\HelperSmartProcess'       => 'lib/Core/Helpers/HelperSmartProcess.php',
        $namespace.'\Core\Helpers\HelperTelegram'           => 'lib/Core/Helpers/HelperTelegram.php',
        $namespace.'\Core\Helpers\HelperUser'               => 'lib/Core/Helpers/HelperUser.php',
        $namespace.'\Core\Helpers\HelperVK'                 => 'lib/Core/Helpers/HelperVK.php',

        $namespace.'\Core\Crm\Component\EntityList\Block'   => 'lib/Core/Crm/Component/EntityList/Block.php',
        $namespace.'\Core\Crm\Component\EntityList\Manager' => 'lib/Core/Crm/Component/EntityList/Manager.php',
    ];
}