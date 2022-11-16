<?php


namespace Intranet\Componentes;

use Illuminate\Support\Facades\Mail;
use Intranet\Entities\Activity;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;
use function authUser,collect,view;


class MyMail
{

    private $elements;
    private $features;
    private $view;
    private $template=null;
    private $attach;
    private $editable;

    public function __get($key)
    {
        return $this->$key??($this->features[$key]??null);
    }

    public function __set($key,$value)
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        } else {
            $this->features[$key] = $value;
        }

    }

    public function __construct($elements=null,$view=null,$features=[],$attach=null,$editable=null)
    {
        $this->features = $features;
        $this->from = !isset($this->features['from'])?authUser()->email:$this->from;
        $this->fromPerson = !isset($this->features['fromPerson'])?authUser()->FullName:$this->fromPerson;
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
        $this->editable = $editable;
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
            $element = $this->class::find($id);
            if (!isset($element)){
                return null;
            }
            if (isset($toCompost[1]) && strpos($toCompost[1],';')) {
                $email = explode(';', $toCompost[1]);
                $element->mail = $email[0];
                $element->contact = $email[1];

            }
            return $element;
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
        if (is_iterable($this->elements)) {
            foreach ($this->elements as $elemento) {
                $this->sendMail($elemento, $fecha);
            }
        }
        else {
            $this->sendMail($this->elements, $fecha);
        }
    }

    private function sendMail($elemento,$fecha){
        if (isset($elemento->contacto)) {
            $mail = $elemento->mail??$elemento->email;
            $contacto = $elemento->contact??$elemento->contacto;
            if (filter_var($mail,FILTER_VALIDATE_EMAIL)) {
                Mail::to($mail, $contacto)
                    ->bcc($this->from)
                    ->send(new DocumentRequest($this, $this->chooseView(), $elemento, $this->attach));
                Alert::info('Enviat correus ' . $this->subject . ' a ' . $contacto);
                if ($this->register) {
                    Activity::record('email', $elemento, null, $fecha, $this->subject);
                }
            }
            else {
                Alert::danger("No s'ha pogut enviar correu a $contacto. Comprova email");
            }
        } else {
            if (isset($elemento)) {
                Alert::info("No s'ha pogut enviar. Falta contacte");
            }
        }
    }

    private function chooseView(){
        if (strlen($this->view)> 50) {
            return 'email.standard';
        }
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