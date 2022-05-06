<?php

namespace App\Security\Csp;

use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Symfony\Component\Intl\Scripts;

class NovaPolicies extends Policy
{
    public function configure(){

        $this->setDefaultPolicies();

        $this->addDirective(Directive::STYLE, [Keyword::UNSAFE_INLINE]);
        $this->addDirective(Directive::OBJECT, [Keyword::SELF]);
        $this->addGoogleFontPolicies();
        $this->addNovaResourcesPolicies();
    }

    private function setDefaultPolicies()
    {
        return
            $this->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, [Keyword::SELF, 'data:'])
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::SCRIPT, Keyword::SELF)
            ->addDirective(Directive::STYLE, Keyword::SELF)
            ->addNonceForDirective(Directive::SCRIPT);
    }

    private function addGoogleFontPolicies()
    {
        $this->addDirective(Directive::FONT, [
            'self',
            'fonts.gstatic.com',
            'fonts.googleapis.com',
            'data:',
        ])
        ->addDirective(Directive::STYLE, 'fonts.googleapis.com');
    }

    // For Nova Resources
    private function addNovaResourcesPolicies(){
        $this->addDirective(Directive::SCRIPT,Keyword::UNSAFE_EVAL)
        ->addDirective(Directive::IMG, '*.gravatar.com');
    }
}
