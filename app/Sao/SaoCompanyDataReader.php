<?php

declare(strict_types=1);

namespace Intranet\Sao;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;

/**
 * Lector de dades d'empresa i centre de treball des de SAO.
 */
class SaoCompanyDataReader
{
    private const TD_NTH_CHILD_2 = 'td:nth-child(2)';
    private const TD_NTH_CHILD_3 = 'td:nth-child(3)';
    private const TD_NTH_CHILD_4 = 'td:nth-child(4)';
    private const TR_NTH_CHILD_2 = 'tr:nth-child(2)';

    /**
     * Llig les dades d'empresa i centre associades a una FCT SAO.
     *
     * @param RemoteWebDriver $driver
     * @param int|string $idFctSao
     * @param int|string|null $idCentroSao
     * @return array<string, mixed>
     */
    public function readFromFct(RemoteWebDriver $driver, int|string $idFctSao, int|string|null $idCentroSao = null): array
    {
        $baseUrl = (string) config('sao.urls.base', 'https://foremp.edu.gva.es');
        $driver->navigate()->to("$baseUrl/index.php?accion=10&idFct=$idFctSao");
        sleep((int) config('sao.navigation.sleep_seconds', 1));

        $data = [
            'empresa' => $this->readEmpresaFromFctPage($driver),
            'centre' => $this->readCentroFromFctPage($driver),
        ];

        if ($idCentroSao !== null && $idCentroSao !== '') {
            $driver->navigate()->to("$baseUrl/index.php?accion=34&idCT=$idCentroSao");
            sleep((int) config('sao.navigation.sleep_seconds', 1));
            $data['centre'] = array_merge($data['centre'], $this->readCentroFromEditPage($driver));
        }

        return $data;
    }

    /**
     * Llig les dades d'empresa visibles en la fitxa FCT.
     *
     * @param RemoteWebDriver $driver
     * @return array<string, mixed>
     */
    private function readEmpresaFromFctPage(RemoteWebDriver $driver): array
    {
        $data = [
            'idSao' => $this->attribute($driver, '#empresaFCT', 'value'),
            'concierto' => $this->attribute($driver, '#numConciertoEmp', 'value'),
        ];

        $tbody = $this->element($driver, 'td#celdaDatosEmpresa table.infoCentroBD tbody');
        if ($tbody === null) {
            return $data;
        }

        $row2 = $this->child($tbody, self::TR_NTH_CHILD_2);
        if ($row2 !== null) {
            $data['cif'] = $this->childText($row2, 'td:nth-child(1)');
            $data['nombre'] = $this->childText($row2, self::TD_NTH_CHILD_2);
            $data['direccion'] = $this->childText($row2, self::TD_NTH_CHILD_3);
            $data['localidad'] = $this->childText($row2, self::TD_NTH_CHILD_4);
        }

        $row4 = $this->child($tbody, 'tr:nth-child(4)');
        if ($row4 !== null) {
            $data['telefono'] = $this->childText($row4, 'td:nth-child(1)');
            $data['gerente'] = $this->childText($row4, self::TD_NTH_CHILD_2);
            $data['actividad'] = $this->childText($row4, self::TD_NTH_CHILD_3);
            $data['email'] = $this->childText($row4, self::TD_NTH_CHILD_4);
        }

        return $data;
    }

    /**
     * Llig les dades bàsiques del centre visibles en la fitxa FCT.
     *
     * @param RemoteWebDriver $driver
     * @return array<string, mixed>
     */
    private function readCentroFromFctPage(RemoteWebDriver $driver): array
    {
        $data = [
            'horarios' => $this->text($driver, 'table.tablaDetallesFCT tbody tr:nth-child(14) td:nth-child(2)'),
        ];

        $tbody = $this->element($driver, 'td#celdaDatosCT table.infoCentroBD tbody');
        if ($tbody === null) {
            return $data;
        }

        $row2 = $this->child($tbody, self::TR_NTH_CHILD_2);
        if ($row2 !== null) {
            $data['nombre'] = $this->childText($row2, self::TD_NTH_CHILD_2);
            $data['localidad'] = $this->childText($row2, self::TD_NTH_CHILD_3);
            $data['telefono'] = $this->childText($row2, self::TD_NTH_CHILD_4);
            $data['email'] = $this->childText($row2, 'td:nth-child(6)');
        }

        return $data;
    }

    /**
     * Llig les dades editables del centre.
     *
     * @param RemoteWebDriver $driver
     * @return array<string, mixed>
     */
    private function readCentroFromEditPage(RemoteWebDriver $driver): array
    {
        return [
            'direccion' => $this->attribute($driver, "input.campoAlumno[name='direccion']", 'value'),
            'codiPostal' => $this->attribute($driver, "input.campoAlumno[name='cp']", 'value'),
        ];
    }

    private function element(RemoteWebDriver $driver, string $selector): ?RemoteWebElement
    {
        try {
            return $driver->findElement(WebDriverBy::cssSelector($selector));
        } catch (NoSuchElementException) {
            return null;
        }
    }

    private function child(RemoteWebElement $element, string $selector): ?RemoteWebElement
    {
        try {
            return $element->findElement(WebDriverBy::cssSelector($selector));
        } catch (NoSuchElementException) {
            return null;
        }
    }

    private function text(RemoteWebDriver $driver, string $selector): ?string
    {
        return $this->normalize($this->element($driver, $selector)?->getText());
    }

    private function childText(RemoteWebElement $element, string $selector): ?string
    {
        return $this->normalize($this->child($element, $selector)?->getText());
    }

    private function attribute(RemoteWebDriver $driver, string $selector, string $attribute): ?string
    {
        return $this->normalize($this->element($driver, $selector)?->getAttribute($attribute));
    }

    private function normalize(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
