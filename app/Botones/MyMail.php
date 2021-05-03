<?php
/**
 * Created by PhpStorm.
 * Profesor: igomis
 * Date: 2019-04-11
 * Time: 13:47
 */

namespace Intranet\Botones;

use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Activity;
use Illuminate\Support\Facades\Mail;

class MyMail
{

    private $elements;
    private $features;
    private $view;
    private $template=null;
    private $attach;


    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : (isset($this->features[$key]) ? $this->features[$key] : null);
    }

    public function __set($key,$value)
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        } else {
            $this->features[$key] = $value;
        }

    }

    public function __construct($elements=null,$view=null,$features=[],$attach=null)
    {
        $this->features = $features;
        $this->from = !isset($this->features['from'])?AuthUser()->email:$this->from;
        $this->fromPerson = !isset($this->features['fromPerson'])?AuthUser()->FullName:$this->fromPerson;
        if (is_object($elements)){
            $this->elements = $elements;
            $this->class = get_class($this->elements->first());
        }
        else{
            $this->elements = $this->recoveryObjects($elements);
        }
        if (is_array($view)){
            $this->view = $view['view'];
            $this->template = $view['template'];
        } else {
            $this->view = $view;
        }
        $this->attach =$attach;
    }

    private function recoveryObjects($elements){
        $objects = collect();
        foreach ( explode(',',$elements) as $element){
            $objects->push($this->recoveryObject($element));
        }
        return $objects;
    }
    private function recoveryObject($element){
        if ($element != '') {
            $toCompost = explode('(', $element);
            $id = $toCompost[0];
            if (isset($toCompost[1])) return $this->class::find($id);
        }
        return null;
    }


    public function render($route){
        $to  = $this->getReceivers($this->elements);
        $editable = (count($this->elements) > 1)?$this->editable:true;
        $from = $this->from;
        $subject = $this->subject;
        $contenido = $this->view;
        $fromPerson = $this->fromPerson;
        $toPeople = $this->toPeople;
        $class = $this->class;
        $register = $this->register;
        $template = $this->template;

        return view('email.view',compact('to','from','subject','contenido','route','fromPerson','toPeople','class','register','editable','template'));
    }

    public function send($fecha=null){
        if (is_iterable($this->elements))
            foreach ($this->elements as $elemento) {
                $this->sendMail($elemento,$fecha);
            }
        else $this->sendMail($this->elements,$fecha);
    }

    private function sendMail($elemento,$fecha){
        if (isset($elemento->contacto)  ) {
            if (filter_var($elemento->email,FILTER_VALIDATE_EMAIL)) {
                Mail::to($elemento->email, $elemento->contacto)
                    ->bcc($this->from)
                    ->send(new DocumentRequest($this, $this->chooseView(), $elemento, $this->attach));
                Alert::info('Enviat correus ' . $this->subject . ' a ' . $elemento->contacto);
                if ($this->register) {
                    Activity::record('email', $elemento, null, $fecha, $this->subject);
                }
            }
            else Alert::danger("No s'ha pogut enviar correu a $elemento->contacto. Comprova email");
        }

    }

    private function chooseView(){
        if (strlen($this->view)> 50) return'email.standard';
        return $this->view;
    }

    private function getReceivers($elementos){
        $to = '';
        foreach ($elementos as $elemento){
            $to .= $this->getReceiver($elemento).',';
        }
        return $to;
    }

    private function getReceiver($elemento){
        return $elemento->id.'('.$elemento->email.';'.$elemento->contacto.')';
    }

    /**
     * @return null
     */
    public function getTo()
    {
        return $this->elements;
    }

}