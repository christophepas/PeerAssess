<?php

namespace Site\CoreBundle\Twig;

use Michelf\Markdown;

class MarkdownExtension extends \Twig_Extension
{
    public function getFilters ()
    {
        return array(
            new \Twig_SimpleFilter(
                'markdown',
                array($this, 'getMarkdown'),
                array('is_safe' => array('html'))
            )
        );
    }

    public function getMarkdown($markdown)
    {
        return Markdown::defaultTransform($markdown);
    }

    public function getName ()
    {
        return 'markdown';
    }
}
