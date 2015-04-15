<?php
namespace Peerassess\CoreBundle\Entity;

class Locale
{

    const FR = "fr";
    const EN = "en";

    public static function getList ()
    {
        $list = self::getListNotDefault();
        $list[] = FR;
        return $list;
    }

    public static function getListNotDefault ()
    {
        return array(self::EN);
    }
}