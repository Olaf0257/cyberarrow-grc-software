<?php

namespace App\Security\Csp;

use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;


class Policies extends Policy
{
    public function configure(){

        $this->setDefaultPolicies();

        $this->addDirective(Directive::STYLE, [Keyword::UNSAFE_INLINE]);
        $this->addDirective(Directive::OBJECT, [Keyword::SELF]);
        $this->addGoogleFontPolicies();
    }

    private function setDefaultPolicies()
    {
        return
            $this->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, [Keyword::SELF,env('AWS_ENDPOINT'), 'data:'])
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
}
