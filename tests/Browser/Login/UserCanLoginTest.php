<?php

namespace Tests\Browser\Login;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\AdministracionController;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserCanLoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */


    public function testLoginPageIsSeen()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/login')
                ->assertSeeIn('.btn','Profesor');
        });
    }

    function testLoginProfesor(){
        $this->browse(function ($browser) {
            $browser->visit('/profesor/login')
                ->type('codigo','6570')
                ->type('password', 'eiclmp5a')
                ->press('Entra')
                ->assertPathIs('/home');
        });
    }

    /*
    function testBuidaBD(){
        $this->browse(function ($browser) {
            $browser->loginAs('021652470V')
                ->visit('/nuevoCurso')
                ->press('Enviar')
                ->assertPathIs('/nuevoCurso');
        });
    }
     */

}
