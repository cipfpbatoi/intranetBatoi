<?php
/**
 * Created by PhpStorm.
 * User: igomis
 * Date: 2020-07-04
 * Time: 09:54
 */

namespace Intranet\Botones;

use Illuminate\View\Component;

class Modal extends Component
{

    public $name;
    public $title;
    public $action;
    public $message;

    /**
     * Modal constructor.
     * @param $name
     * @param $title
     * @param $action
     * @param $components
     * @param $message
     */
    public function __construct(String $name,String $title,String $message,String $action ="#")
    {
        $this->name = $name;
        $this->title = $title;
        $this->action = $action;
        $this->message = $message;
    }


    public function render()
    {
        return view('batoi-modal::modal');
    }


}