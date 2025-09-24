<?php

namespace Intranet\Componentes;

use Illuminate\Support\Facades\Log;
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
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        return $this->features[$key] ?? null;
    }

    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            $this->features[$key] = $value;
        }
    }

    public function __construct($elements=null, $view=null, $features=[], $attach=null, $editable=null)
    {
        $this->features = $features;
        $this->from = !isset($this->features['from'])?authUser()->email:$this->from;
        $this->fromPerson = !isset($this->features['fromPerson'])?authUser()->FullName:$this->fromPerson;
        if (is_object($elements)) {
            $this->elements = $elements;
            $this->class = get_class($this->elements->first());
        } else {
            $this->elements = $this->recoveryObjects($elements);
        }
        if (is_array($view)) {
            $this->view = $view['view'];
            $this->template = $view['template'];
        } else {
            $this->view = $view;
        }
        $this->attach = $attach??session()->get('attach')??null;
        $this->editable = $editable;

        $this->register = $this->features['register'] ?? null;
    }

    private function recoveryObjects($elements)
    {
        // 🔹 Si `$elements` és `null` o buit, retornem una col·lecció buida
        if (!$elements) {
            return collect();
        }

        // 🔹 Si `$elements` és una col·lecció ja feta, la retornem directament
        if ($elements instanceof \Illuminate\Support\Collection) {
            return $elements;
        }

        // 🔹 Convertim `$elements` en array si és una cadena separada per comes
        $elementsArray = is_string($elements) ? explode(',', $elements) : (array) $elements;

        return collect($elementsArray)
            ->map(fn($element) => $this->recoveryObject($element))
            ->filter(); // Eliminem els valors `null`
    }

    private function recoveryObject($element)
    {
        // 🔹 Si l'element és buit o `null`, retornem `null`
        if (empty($element)) {
            return null;
        }

        // 🔹 Si `$element` ja és un objecte, el retornem directament
        if (is_object($element)) {
            return $element;
        }

        // 🔹 Comprovem si `$this->class` està definit i existeix abans de fer `find()`
        if (!$this->class || !class_exists($this->class)) {
            Log::warning("⚠️ `class` no està definit o no existeix: {$this->class}");
            return null;
        }

        // 🔹 Separem l'ID i la informació de contacte
        [$id, $contactInfo] = array_pad(explode('(', $element, 2), 2, null);
        $id = trim($id);

        // 🔹 Si l'ID no és numèric, retornem `null`
        if (!is_numeric($id)) {
            return null;
        }

        // 🔹 Busquem l'element a la base de dades
        $instance = $this->class::find($id);

        if (!$instance) {
            return null;
        }

        // 🔹 Processar correu i contacte, si estan presents
        if ($contactInfo && strpos($contactInfo, ';') !== false) {
            [$email, $contact] = explode(';', rtrim($contactInfo, ')'), 2);
            $instance->mail = $email;
            $instance->contact = strlen($contact) > 3 ? $contact : null;
        }

        return $instance;
    }


    public function render($route)
    {

        $to = $this->getReceivers($this->elements);
        $editable = (count($this->elements) > 1) ? $this->editable : true;

        // 🔹 Assegurar que `contenido` sempre és una instància de `View`
        $contenido = ($this->view instanceof \Illuminate\View\View)
            ? $this->view
            : (view()->exists($this->view) ? view($this->view) : $this->view);

        $data = [
            'to' => $to,
            'from' => $this->from,
            'subject' => $this->subject ?? null,
            'contenido' => $contenido,
            'route' => $route,
            'fromPerson' => $this->fromPerson,
            'toPeople' => $this->toPeople ?? null,
            'class' => $this->class ?? null,
            'register' => $this->register ?? null,
            'editable' => $editable,
            'template' => $this->template ?? null,
            'action' => $this->action ?? 'myMail.send',
        ];
        if ($this->attach) {
            session()->put('attach', $this->attach);
        }

        Log::info("📌 Dades passades a la vista (modificat):", $data);

        return view('email.view', $data);
    }

    private function  sendEvent($elements){
        if (session()->has('email_action')) {
            $event = session()->get('email_action');
            event(new $event($this->elements));
            session()->forget('email_action');
        }
    }

    public function send($fecha=null)
    {
        if (is_iterable($this->elements)) {
            foreach ($this->elements as $elemento) {
                $this->sendMail($elemento, $fecha);
            }
        } else {
            $this->sendMail($this->elements, $fecha);
        }

        session()->forget('attach');
    }

    private function sendMail($elemento, $fecha)
    {
        $contacto = $elemento->contact??$elemento->contacto??'A qui corresponga';
        if (isset($elemento)){
            $mail = $elemento->mail??$elemento->email;
            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($mail, $contacto)
                    ->bcc($this->from)
                    ->send(new DocumentRequest($this, $this->chooseView(), $elemento, $this->attach));
                Alert::info('Enviat correus ' . $this->subject . ' a ' . $contacto);
                if ($this->register !== null) {
                    Activity::record('email', $elemento, null, $fecha, $this->register);
                }
                $this->sendEvent($elemento);
            } else {
                Alert::danger("No s'ha pogut enviar correu a $contacto. Comprova email");
            }
        }
    }

    private function chooseView()
    {
        if (strlen($this->view)< 50) {
            $viewPath = view($this->view)->getPath();
            $this->view = file_get_contents($viewPath);
        }
        return 'email.standard';
    }

    private function getReceivers($elementos)
    {
        $to = '';
        foreach ($elementos as $elemento) {
            if (isset($elemento)){
                $to .= $this->getReceiver($elemento).',';
            }
        }
        return $to;
    }

    private function getReceiver($elemento)
    {

        $mail = $elemento->mail??$elemento->email;
        $contacto = $elemento->contact??$elemento->contacto;
        return $elemento->id.'('.$mail.';'.$contacto.')';
    }

    /**
     * @return null
     */
    public function getTo()
    {
        return $this->elements;
    }
}