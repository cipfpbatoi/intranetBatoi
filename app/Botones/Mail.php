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

    private $to;
    private $from;
    private $subject;
    private $content;
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
    public function __construct($to=null,$toPeople=null,$subject=null,$content=null,$from=null,$fromPerson=null)
    {
        $this->to = $to;
        $this->from = $from?$from:AuthUser()->email;
        $this->subject = $subject;
        $this->fromPerson = $fromPerson?$fromPerson:AuthUser()->FullName;
        $this->content = $content;
        $this->toPeople = $toPeople;
    }

    public function render($route){
        $to  = $this->to;
        $from = $this->from;
        $subject = $this->subject;
        $content = $this->content;
        $fromPerson = $this->fromPerson;
        $toPeople = $this->toPeople;
        return view('email.view',compact('to','from','subject','content','route','fromPerson','toPeople'));
    }

    public function send(){
        $destinataris = explode(',',$this->to);
        foreach ($destinataris as $to)
            LaravelMail::to($to,$this->toPeople)
                ->send( new DocumentRequest($this,'email.standard'));

        Alert::info('Enviats correus '.$this->subject.' a '.$this->to);
    }

    /**
     * @return null
     */
    public function getTo()
    {
        return $this->to;
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
        return $this->content;
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