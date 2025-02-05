<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\Http\Traits\Crud;


abstract class IntranetController extends BaseController
{
    use Crud;
       
}