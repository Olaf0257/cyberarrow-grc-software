<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MutatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Register your custom accessors/mutators extensions here.

//        Mutator::extend(CleanHtml::class, function($model, $value, $key){
//
//            // sinitize HERE AND RETURN THE VALUE
//            return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
//        });
    }
}
