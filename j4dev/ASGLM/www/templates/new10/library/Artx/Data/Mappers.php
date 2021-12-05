<?php

Artx::load('Artx_Data_CategoryMapper');
Artx::load('Artx_Data_ContentMapper');
Artx::load('Artx_Data_ExtensionMapper');
Artx::load('Artx_Data_MenuItemMapper');
Artx::load('Artx_Data_MenuMapper');
Artx::load('Artx_Data_ModuleMapper');

class Artx_Data_Mappers
{
    public static function errorCallback($callback, $get = false)
    {
        static $errorCallback;
        if (!$get)
            $errorCallback = $callback;
        return $errorCallback;
    }

    public static function get($name)
    {
        $className = 'Artx_Data_' . ucfirst($name) . 'Mapper';
        $mapper = new $className();
        return $mapper;
    }

    public static function error($error, $code)
    {
        $null = null;
        $callback = Artx_Data_Mappers::errorCallback($null, true);
        if (isset($callback))
            call_user_func($callback, $error, $code);
        return $error;
    }
}
