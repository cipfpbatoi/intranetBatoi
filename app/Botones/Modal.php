<?php
/**
 * Created by PhpStorm.
 * Profesor: igomis
 * Date: 2020-07-04
 * Time: 09:54
 */

namespace Intranet\Botones;

use Illuminate\View\Component;
use function view;

class Modal extends Component
{

    public $name;
    public $title;
    public $action;
    public $message;
    public $clase;
    public $cancel;

    /**
     * Modal constructor.
     * @param $name
     * @param $title
     * @param $action
     * @param $components
     * @param $message
     * @param $clase
     */
    public function __construct(String $name,String $title,String $message,String $action ="#",String $clase='',$cancel='Cancelar')
    {
        $this->name = $name;
        $this->title = $title;
        $this->action = $action;
        $this->message = $message;
        $this->clase = $clase;
        $this->cancel = $cancel;
    }


    public function render()
    {
        return view('batoiModal.modal');
    }


}