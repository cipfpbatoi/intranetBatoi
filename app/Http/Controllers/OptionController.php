<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Http\Requests\OptionStoreRequest;
use Intranet\Entities\Poll\Option;

/**
 * Class OptionController
 * @package Intranet\Http\Controllers
 */
class OptionController extends ModalController
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
     * @param OptionStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OptionStoreRequest $request)
    {
        $this->authorize('create', Option::class);
        $this->persist($request);
        return redirect()->action('PPollController@show', ['id' => $request->ppoll_id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $option = Option::findOrFail((int) $id);
        $this->authorize('delete', $option);
        $poll = $option->ppoll_id;
        parent::destroy($id);
        return redirect()->action('PPollController@show', ['id' => $poll]);
    }
    
    
}
