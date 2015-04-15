<?php

namespace Site\CoreBundle\Twig;

use Peerassess\CoreBundle\Entity\Languages;

/**
 *
 *
 */
class LanguageExtension extends \Twig_Extension
{

    public function getFilters ()
    {
        return array(
                new \Twig_SimpleFilter('language', array(
                        $this,
                        'getLanguageName'
                ), array(
                        'is_safe' => array(
                                'html'
                        )
                ))
        );
    }

    public function getLanguageName($language)
    {
        return Languages::getLanguage($language);
    }

    public function getName ()
    {
        return 'language';
    }
}
