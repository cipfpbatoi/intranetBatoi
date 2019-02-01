<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Poll\Option;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;

class OptionController extends IntranetController
{
    protected $namespace = 'Intranet\Entities\Poll\\'; //string on es troben els models de dades
    protected $perfil = 'profesor';
    protected $model = 'Option';
    protected $gridFields = [ 'question','scala'];
    
    public function store(Request $request)
    {
        parent::store($request);
        return redirect()->action('PollController@show', ['id' => $request->poll_id]);
    }
    public function destroy($id)
    {
        $poll = Option::find($id)->poll_id;
        parent::destroy($id);
        return redirect()->action('PollController@show', ['id' => $poll]);
    }
    
    
}
