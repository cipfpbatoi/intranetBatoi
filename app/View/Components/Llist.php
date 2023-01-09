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

class Llist extends Component
{

    public $image;
    public $date;

    /**
     * @param $image
     * @param $fecha
     */
    public function __construct($image, $date)
    {
        $this->image = $image;
        $this->date = $date;
    }


    public function render()
    {
        return view('components.llist');
    }
}
