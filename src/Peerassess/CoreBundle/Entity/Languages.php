<?php
namespace Peerassess\CoreBundle\Entity;

class Languages
{

    const HTML = 0;
    const JAVASCRIPT = 1;
    const ANGULAR = 2;
    const NODEJS = 3;
    const PHP = 4;
    const SYMFONY = 5;
    const RUBY = 6;
    const ROR = 7;
    const PYTHON = 8;
    const DJANGO = 9;
    const JAVA = 10;
    const OBJECTIVEC = 11;
    const SWIFT = 12;
    const OTHER = 13;

    public static function getLanguage ($language)
    {
        return self::getList()[$language];
    }

    public static function getList ()
    {
        return array(
            self::HTML => "HTML",
            self::JAVASCRIPT => "Javascript",
            self::ANGULAR => "Angular",
            self::NODEJS => "Node JS",
            self::PHP => "PHP",
            self::SYMFONY => "Symfony",
            self::RUBY => "Ruby",
            self::ROR => "Ruby on Rails",
            self::PYTHON => "Python",
            self::DJANGO => "Django",
            self::JAVA => "Java",
            self::OBJECTIVEC => "Objective C",
            self::SWIFT => "Swift",
            self::OTHER => "Other",
        );
    }

    public static function getLanguageCode ($language)
    {
        return self::getListCodes()[$language];
    }

    public static function getListCodes ()
    {
        return array(
                "HTML" => self::HTML,
                "Javascript" => self::JAVASCRIPT,
                "Angular" => self::ANGULAR,
                "Node JS" => self::NODEJS,
                "PHP" => self::PHP,
                "Symfony" => self::SYMFONY,
                "Ruby" => self::RUBY,
                "Ruby on Rails" => self::ROR,
                "Python" => self::PYTHON,
                "Django" => self::DJANGO,
                "Java" => self::JAVA,
                "Objective C" => self::OBJECTIVEC,
                "Swift" => self::SWIFT,
                "Other" => self::OTHER,
        );
    }
}
