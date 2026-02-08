<?php

namespace Intranet\Services\Mail;

use Intranet\Services\Mail\MailSender;
use Intranet\Services\Mail\RecipientResolver;
use function authUser,view;

/**
 * Correu compost a partir de dades de configuració i receptors.
 *
 * Responsabilitats:
 * - Mantindre les dades del correu.
 * - Preparar la vista per a previsualització (render).
 * - Delegar l'enviament a MailSender.
 */
class MyMail
{
    /** @var \Illuminate\Support\Collection|iterable|object|null */
    private $elements;
    /** @var array */
    private $features;
    /** @var string|\Illuminate\View\View|null */
    private $view;
    /** @var string|null */
    private $template = null;
    /** @var array|null */
    private $attach;
    /** @var bool|null */
    private $editable;
    /** @var RecipientResolver */
    private $resolver;
    /** @var MailSender */
    private $sender;

    /**
     * Retorna propietats internes o del mapa de característiques.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        return $this->features[$key] ?? null;
    }

    /**
     * Assigna propietats internes o del mapa de característiques.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            $this->features[$key] = $value;
        }
    }

    /**
     * @param mixed $elements Elements, col·lecció o llista d'IDs.
     * @param mixed $view Vista o array amb ['view' => ..., 'template' => ...].
     * @param array $features Característiques del correu (subject, from, register, etc.).
     * @param array|null $attach Adjunts (path => mime).
     * @param bool|null $editable Indica si el contingut és editable.
     * @param RecipientResolver|null $resolver Resolvedor de receptors.
     * @param MailSender|null $sender Enviador de correus.
     */
    public function __construct(
        $elements = null,
        $view = null,
        $features = [],
        $attach = null,
        $editable = null,
        RecipientResolver $resolver = null,
        MailSender $sender = null
    )
    {
        $this->features = $features;
        $this->from = !isset($this->features['from'])?authUser()->email:$this->from;
        $this->fromPerson = !isset($this->features['fromPerson'])?authUser()->FullName:$this->fromPerson;
        $this->resolver = $resolver ?? new RecipientResolver();
        $this->sender = $sender ?? new MailSender();
        if (is_object($elements)) {
            $this->elements = $elements;
            $this->class = get_class($this->elements->first());
        } else {
            $this->elements = $this->resolver->resolveElements($elements, $this->class ?? null);
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

    /**
     * Renderitza la vista d'edició del correu.
     *
     * @param string $route
     * @return \Illuminate\Contracts\View\View
     */
    public function render($route)
    {

        $to = $this->resolver->formatReceivers($this->elements);
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

        //Log::info("📌 Dades passades a la vista (modificat):", $data);

        return view('email.view', $data);
    }

    /**
     * Envia el correu a tots els receptors.
     *
     * @param mixed $fecha
     * @return void
     */
    public function send($fecha = null)
    {
        $this->sender->send($this, $fecha);
    }

    /**
     * Resol la vista a enviar (carrega el fitxer si cal).
     *
     * @return string
     */
    public function resolveViewForSend()
    {
        if (strlen($this->view)< 50) {
            $viewPath = view($this->view)->getPath();
            $this->view = file_get_contents($viewPath);
        }
        return 'email.standard';
    }

    /**
     * Retorna la col·lecció o element(s) a qui s'enviarà el correu.
     *
     * @return mixed
     */
    public function getTo()
    {
        return $this->elements;
    }
}
