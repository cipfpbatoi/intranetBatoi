<?php
/**
 * Created by PhpStorm.
 * Profesor: igomis
 * Date: 2020-07-04
 * Time: 09:54
 */

namespace Intranet\View\Components;

use Illuminate\View\Component;
use function view;

class Note extends Component
{

    public $name;
    public $title;
    public $message;
    public $color;
    public $linkEdit;
    public $linkShow;

    /**
     * @param $name
     * @param $title
     * @param $message
     * @param $linkEdit
     * @param $linkShow
     */
    public function __construct($name, $title, $message, $color, $linkEdit='#', $linkShow='#')
    {
        $this->name = $name;
        $this->title = $title;
        $this->message = $message;
        $this->color = $color;
        $this->linkEdit = $linkEdit;
        $this->linkShow = $linkShow;
    }

    public function render()
    {
        return view('components.note');
    }
}
