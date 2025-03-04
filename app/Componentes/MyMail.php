<?php

namespace Intranet\Componentes;

use Illuminate\Support\Facades\Mail;
use Intranet\Entities\Activity;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;
use function authUser, collect, view;

class MyMail
{
    private $elements;
    private $features;
    private $view;
    private $template = null;
    private $attach;
    private $editable;
    private $from;
    private $fromPerson;
    private $register;

    public function __construct($elements = null, $view = null, $features = [], $attach = null, $editable = null)
    {
        $this->features = $features;
        $this->from = $features['from'] ?? authUser()->email;
        $this->fromPerson = $features['fromPerson'] ?? authUser()->FullName;
        $this->elements = is_object($elements) ? collect($elements) : $this->recoveryObjects($elements);
        $this->class = $this->elements->isNotEmpty() ? get_class($this->elements->first()) : null;
        $this->view = is_array($view) ? $view['view'] : $view;
        $this->template = is_array($view) ? $view['template'] : null;
        $this->attach = $attach ?? session()->get('attach');
        $this->editable = $editable;
        $this->register = $features['register'] ?? null;
    }

    private function recoveryObjects($elements)
    {
        if (!$elements) {
            return collect();
        }

        return collect(is_array($elements) ? $elements : explode(',', $elements))
            ->map(fn($element) => $this->recoveryObject($element))
            ->filter(); // Eliminem valors null
    }

    private function recoveryObject($element)
    {
        if (is_object($element)) {
            return $element;
        }

        if (!is_string($element) || empty($element) || !class_exists($this->class)) {
            return null;
        }

        [$id, $contactInfo] = array_pad(explode('(', $element, 2), 2, null);
        $instance = $this->class::find($id);

        if (!$instance) {
            return null;
        }

        if ($contactInfo && strpos($contactInfo, ';') !== false) {
            [$email, $contact] = explode(';', rtrim($contactInfo, ')'));
            $instance->contact = strlen($contact) > 3 ? $contact : null;
            $instance->mail = $email;
        }

        return $instance;
    }

    public function render($route)
    {
        $data = [
            'to' => $this->getFormattedReceivers(),
            'from' => $this->from,
            'subject' => $this->features['subject'] ?? null,
            'contenido' => $this->view,
            'route' => $route,
            'fromPerson' => $this->fromPerson,
            'toPeople' => $this->features['toPeople'] ?? null,
            'class' => $this->class,
            'register' => $this->register,
            'editable' => count($this->elements) > 1 ? $this->editable : true,
            'template' => $this->template,
            'action' => $this->features['action'] ?? 'myMail.send',
        ];

        if ($this->attach) {
            session()->put('attach', $this->attach);
        }

        return view('email.view', $data)->render();
    }

    private function sendEvent()
    {
        if (session()->has('email_action')) {
            event(new (session()->pull('email_action'))($this->elements));
        }
    }

    public function send($fecha = null)
    {
        if ($this->elements->isEmpty()) {
            return;
        }

        $this->elements->each(fn($element) => $this->sendMail($element, $fecha));
        session()->forget('attach');
    }

    private function sendMail($element, $fecha)
    {
        if (!isset($element)) {
            return;
        }

        $contacto = $element->contact ?? $element->contacto ?? 'A qui corresponga';
        $mail = $element->mail ?? $element->email;

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            Alert::danger("No s'ha pogut enviar correu a $contacto. Comprova email");
            return;
        }

        Mail::to($mail, $contacto)
            ->bcc($this->from)
            ->send(new DocumentRequest($this, $this->chooseView(), $element, $this->attach));

        $subject = $this->features['subject'] ?? 'Sense assumpte';
        Alert::info("Enviat correus {$subject} a $contacto");

        if ($this->register !== null) {
            Activity::record('email', $element, null, $fecha, $this->register);
        }

        $this->sendEvent();
    }

    private function chooseView()
    {
        return view()->exists($this->view) ? $this->view : 'email.standard';
    }

    private function getReceivers()
    {
        return $this->elements
            ->map(fn($element) => $this->getReceiver($element))
            ->implode(',');
    }

    private function getReceiver($element)
    {
        return "{$element->id}({$element->mail};{$element->contact})";
    }

    public function getFormattedReceivers()
    {
        return $this->getReceivers();
    }

    public function getTo()
    {
        return $this->elements;
    }
}
