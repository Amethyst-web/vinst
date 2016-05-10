<?php

abstract class shopPlugin extends waPlugin
{
    protected static function getPluginTemplatePath($templateName = 'default.html'){
        return 'wa-apps/'.static::$selfInfo['app_id'].'/plugins/'.static::$selfInfo['id'].'/templates/'.$templateName;
    }
}
