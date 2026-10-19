<?php

declare(strict_types=1);

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

/**
 * Captura les pantalles principals del professorat amb Selenium.
 *
 * Les credencials i les adreces es reben per variables d'entorn per evitar
 * guardar secrets o dependències de la instal·lació local al repositori.
 */

$baseUrl = rtrim((string) getenv('DOCS_BASE_URL'), '/');
$seleniumUrl = rtrim((string) getenv('DOCS_SELENIUM_URL'), '/');
$login = trim((string) getenv('DOCS_LOGIN'));
$password = (string) getenv('DOCS_PASSWORD');
$requestedPages = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) getenv('DOCS_PAGES'))
)));

if ($baseUrl === '' || $seleniumUrl === '' || $login === '' || $password === '') {
    fwrite(
        STDERR,
        "Cal definir DOCS_BASE_URL, DOCS_SELENIUM_URL, DOCS_LOGIN i DOCS_PASSWORD.\n"
    );
    exit(1);
}

$outputDirectory = dirname(__DIR__, 2) . '/docs/img/ajuda/actual';
if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
    fwrite(STDERR, "No s'ha pogut crear {$outputDirectory}.\n");
    exit(1);
}

$pages = [
    'inici' => '/home',
    'perfil' => '/perfil',
    'grups' => '/grupo',
    'moduls' => '/modulogrupo',
    'guardies' => '/guardia',
    'seguiments' => '/resultado',
    'activitats' => '/actividad',
    'comissions' => '/comision',
    'assumptes-particulars' => '/assumptes-particulars',
    'absencies' => '/falta',
    'incidencies' => '/incidencia',
    'reserves' => '/reserva',
    'reunions' => '/reunion',
    'contactes-fct' => '/colaboracion',
    'meues-colaboracions' => '/misColaboraciones',
    'tutories' => '/tutoria',
    'cap-departament' => '/resultado/list',
    'empreses-fct' => '/empresa',
    'panell-fct' => '/fct',
    'alumnat-fct' => '/alumnofct',
    'avaluacio-fct' => '/avalFct',
    'signatures-fct' => '/signatura',
    'direccio-activitats' => '/direccion/actividad',
    'direccio-comissions' => '/direccion/comision',
    'direccio-absencies' => '/direccion/falta',
    'direccio-assumptes-particulars' => '/direccion/assumptes-particulars',
    'direccio-programacions' => '/direccion/programacion/list',
    'direccio-professorat' => '/direccion/profesor',
];

$firefoxOptions = (new FirefoxOptions())->addArguments([
    '--width=1600',
    '--height=1000',
]);
$capabilities = DesiredCapabilities::firefox();
$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
$capabilities->setCapability('acceptInsecureCerts', true);

$driver = RemoteWebDriver::create($seleniumUrl, $capabilities, 15000, 120000);

try {
    $driver->manage()->window()->setSize(new WebDriverDimension(1600, 1000));
    $driver->get($baseUrl . '/profesor/login');
    $driver->wait(15)->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('codigo'))
    );

    sanitizePage($driver);
    $driver->takeScreenshot($outputDirectory . '/01-login-professorat.png');

    $driver->findElement(WebDriverBy::name('codigo'))->sendKeys($login);
    $driver->findElement(WebDriverBy::name('password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::cssSelector('button[type="submit"], input[type="submit"]'))->click();

    $driver->wait(20)->until(
        static fn (RemoteWebDriver $browser): bool => !str_contains($browser->getCurrentURL(), '/profesor/login')
    );

    $position = 2;
    foreach ($pages as $name => $path) {
        if ($requestedPages !== [] && !in_array($name, $requestedPages, true)) {
            $position++;
            continue;
        }

        $driver->get($baseUrl . $path);
        $driver->wait(20)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
        usleep(1_500_000);

        if ($name === 'inici') {
            $driver->findElement(WebDriverBy::cssSelector('a[href="#tab_content4"]'))->click();
            usleep(500_000);
        }

        sanitizePage($driver);
        $filename = sprintf('%02d-%s.png', $position, $name);
        $driver->takeScreenshot($outputDirectory . '/' . $filename);

        $currentPath = (string) parse_url($driver->getCurrentURL(), PHP_URL_PATH);
        fwrite(STDOUT, "{$filename}\t{$currentPath}\n");
        $position++;
    }
} finally {
    try {
        $driver->quit();
    } catch (Throwable) {
        // Selenium pot haver tancat la sessió després d'un timeout de navegador.
    }
}

/**
 * Oculta fotos i substitueix identificadors personals que encara puguen
 * aparéixer en textos generats abans de prendre la captura.
 */
function sanitizePage(RemoteWebDriver $driver): void
{
    $driver->executeScript(<<<'JAVASCRIPT'
        window.scrollTo(0, 0);

        const style = document.createElement('style');
        style.textContent = `
            #phpdebugbar, .phpdebugbar, .sf-toolbar, iframe[src*="debugbar"] {
                display: none !important;
            }
            #dni, #rol, img[src*="/storage/fotos/"], img[alt="FotoUsuari"] {
                display: none !important;
            }
        `;
        document.head.appendChild(style);

        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        for (const node of nodes) {
            const original = node.nodeValue || '';
            node.nodeValue = original
                .replace(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/gi, 'usuari@example.test')
                .replace(/\b(?:\d{8,9}[A-Z]|[XYZ]\d{7}[A-Z])\b/gi, '00000000T')
                .replace(/\b[679]\d{8}\b/g, '600000000');
        }
    JAVASCRIPT);
}
