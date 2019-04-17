<?php
/**
 * Created by PhpStorm.
 * User: igomis
 * Date: 2019-04-11
 * Time: 13:47
 */

namespace Intranet\Botones;

use Mail as LaravelMail;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;

class Mail
{

    private $elements;
    private $from;
    private $subject;
    private $view;
    private $fromPerson;
    private $toPeople;

    /**
     * Mail constructor.
     * @param $to
     * @param $from
     * @param $subject
     * @param $content
     * @param $route
     */
    public function __construct($elements=null,$toPeople=null,$subject=null,$view=null,$from=null,$fromPerson=null)
    {
        $this->elements = $elements;
        $this->from = $from?$from:AuthUser()->email;
        $this->subject = $subject;
        $this->fromPerson = $fromPerson?$fromPerson:AuthUser()->FullName;
        $this->view = $view;
        $this->toPeople = $toPeople;
    }

    public function render($route){
        $to  = $this->getReceivers($this->elements);
        $from = $this->from;
        $subject = $this->subject;
        $content = $this->view;
        $fromPerson = $this->fromPerson;
        $toPeople = $this->toPeople;
        return view('email.view',compact('to','from','subject','content','route','fromPerson','toPeople'));
    }

    public function send(){
        foreach ( explode(',',$this->elements) as $destinatari) $this->sendMail($destinatari);
        Alert::info('Enviats correus '.$this->subject.' a '.$this->elements);
    }

    public function sendMail($destinatari){
        if ($destinatari != ''){
            $toCompost = explode('(',$destinatari);
            $to = $toCompost[0];
            $contact = substr($toCompost[1],0,strlen($toCompost[1])-1);
            LaravelMail::to($to,$this->toPeople)
                ->send( new DocumentRequest($this,'email.standard',$contact));
        }
    }

    public function renderAndSend(){
        foreach ($this->elements as $elemento){
            LaravelMail::to('igomis@cipfpbatoi.es','Ignasi Gomis Mullor')
                ->send( new DocumentRequest($this,$this->view,$elemento));

        }
    }

    private function getReceivers($elementos){
        $to = '';
        foreach ($elementos as $elemento){
            $to .= $this->getReceiver($elemento).',';
        }
        return $to;
    }

    private function getReceiver($elemento){
        return $elemento->email.'('.$elemento->contacto.')';
    }

    /**
     * @return null
     */
    public function getTo()
    {
        return $this->elements;
    }

    /**
     * @return |null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->view;
    }

    /**
     * @return |null
     */
    public function getFromPerson()
    {
        return $this->fromPerson;
    }

    /**
     * @return null
     */
    public function getToPeople()
    {
        return $this->toPeople;
    }


}