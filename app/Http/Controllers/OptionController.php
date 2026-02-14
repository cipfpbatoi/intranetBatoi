<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;

/**
 * Class OptionController
 * @package Intranet\Http\Controllers
 */
class OptionController extends IntranetController
{
    /**
     * @var string
     */
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Option';
    /**
     * @var array
     */
    protected $gridFields = [ 'question','scala'];

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        return redirect()->action('PPollController@show', ['id' => $request->ppoll_id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $poll = Option::find($id)->ppoll_id;
        parent::destroy($id);
        return redirect()->action('PPollController@show', ['id' => $poll]);
    }
    
    
}
