<?php

namespace Intranet\Services\Mail;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use function collect;

/**
 * Resol i formata receptors per a MyMail.
 */
class RecipientResolver
{
    /**
     * Converteix una llista d'elements en col·lecció d'objectes.
     *
     * @param mixed $elements
     * @param string|null $class Class per recuperar elements amb find().
     * @return \Illuminate\Support\Collection
     */
    public function resolveElements($elements, $class = null)
    {
        if (!$elements) {
            return collect();
        }

        if ($elements instanceof Collection) {
            return $elements;
        }

        $elementsArray = is_string($elements) ? explode(',', $elements) : (array) $elements;

        return collect($elementsArray)
            ->map(fn($element) => $this->resolveElement($element, $class))
            ->filter();
    }

    /**
     * Resol un element a objecte, si cal.
     *
     * @param mixed $element
     * @param string|null $class
     * @return mixed|null
     */
    public function resolveElement($element, $class = null)
    {
        if (empty($element)) {
            return null;
        }

        if (is_object($element)) {
            return $element;
        }

        if (!$class || !class_exists($class)) {
            Log::warning("⚠️ `class` no està definit o no existeix: {$class}");
            return null;
        }

        [$id, $contactInfo] = array_pad(explode('(', $element, 2), 2, null);
        $id = trim($id);

        if (!is_numeric($id)) {
            return null;
        }

        $instance = $class::find($id);

        if (!$instance) {
            return null;
        }

        if ($contactInfo && strpos($contactInfo, ';') !== false) {
            [$email, $contact] = explode(';', rtrim($contactInfo, ')'), 2);
            $instance->mail = $email;
            $instance->contact = strlen($contact) > 3 ? $contact : null;
        }

        return $instance;
    }

    /**
     * Dona format a la llista de receptors per a la vista.
     *
     * @param iterable $elements
     * @return string
     */
    public function formatReceivers($elements)
    {
        $to = '';
        foreach ($elements as $element) {
            if (isset($element)) {
                $to .= $this->formatReceiver($element) . ',';
            }
        }
        return $to;
    }

    /**
     * Dona format a un receptor: id(mail;contacte).
     *
     * @param object $element
     * @return string
     */
    public function formatReceiver($element)
    {
        $mail = $element->mail ?? $element->email;
        $contact = $element->contact ?? $element->contacto;
        return $element->id . '(' . $mail . ';' . $contact . ')';
    }
}
