<?php

namespace Tests\Browser;

use Intranet\Application\Colaboracion\ColaboracionService;
use Intranet\Entities\Profesor;
use Laravel\Dusk\Browser;
use Illuminate\Support\Str;
use Throwable;
use Tests\DuskTestCase;

/**
 * Proves Dusk de comportament JS en el panell de misColaboraciones.
 */
class ColaboracionBookInteractionTest extends DuskTestCase
{
    /**
     * Verifica que, després de guardar "book", el nou "+" dinàmic obri modal
     * i carrega el comentari sense recarregar la pàgina.
     */
    public function test_book_crea_plus_dinamic_i_mostra_comentari_en_modal(): void
    {
        $profesor = $this->profesorAmbColaboracionsOrSkip();
        if ($profesor === null) {
            return;
        }

        $comment = 'Dusk book '.date('YmdHis');
        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($profesor, $comment, $login) {
            $browser->visit('/profesor/login')
                ->type('codigo', $login['identifier'])
                ->type('password', $login['password'])
                ->press('Entra')
                ->pause(1500)
                ->assertDontSee('Login Profesor')
                ->visit('/misColaboraciones')
                ->pause(1800)
                ->assertPathIs('/misColaboraciones')
                ->assertPresent('.book');

            $browser->script(<<<'JS'
window.__duskAjaxBook = [];
if (typeof window.jQuery !== 'undefined') {
  window.jQuery(document).off('ajaxComplete.__duskBook');
  window.jQuery(document).on('ajaxComplete.__duskBook', function (_evt, xhr, settings) {
    if (!settings || typeof settings.url !== 'string') return;
    if (settings.url.indexOf('/api/colaboracion/') === -1 || settings.url.indexOf('/book') === -1) return;
    window.__duskAjaxBook.push({
      url: settings.url,
      status: xhr ? xhr.status : null,
      response: xhr ? String(xhr.responseText || '').slice(0, 300) : ''
    });
  });
}
JS
            );

            $clicked = $browser->script(<<<'JS'
const allBooks = Array.from(document.querySelectorAll('.book'));
const visibleBooks = allBooks.filter((btn) => {
  if (!btn) return false;
  if (btn.offsetParent === null) return false;
  const style = window.getComputedStyle(btn);
  return style.display !== 'none' && style.visibility !== 'hidden';
});
const target = visibleBooks[0] || allBooks[0] || null;
if (!target) {
  return false;
}
target.scrollIntoView({behavior: 'instant', block: 'center'});
if (typeof window.jQuery !== 'undefined') {
  window.jQuery(target).trigger('click');
} else {
  target.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
}
return true;
JS
            )[0] ?? false;

            $this->assertTrue((bool) $clicked, 'No s\'ha trobat cap botó .book al DOM.');

            $browser->waitFor('#dialogo', 10)
                ->type('#dialogo #explicacion', $comment)
                ->press('#dialogo button[type="submit"]')
                ->pause(1400);

            $browser->waitUsing(10, 200, function () use ($browser, $profesor, $comment): bool {
                return is_numeric($this->bookActivityIdByCommentForProfesor($profesor->dni, $comment));
            }, 'No s\'ha persistit l\'activitat "book" en el temps esperat.');

            $createdActivityId = $this->bookActivityIdByCommentForProfesor($profesor->dni, $comment);
            if (!is_numeric($createdActivityId)) {
                $ajaxBook = $browser->script(
                    "return window.__duskAjaxBook ? JSON.stringify(window.__duskAjaxBook) : '[]';"
                )[0] ?? '[]';
                $this->fail(
                    'No s\'ha guardat cap activitat "book" amb el comentari enviat. '.
                    'Traça AJAX book: '.(string) $ajaxBook
                );
            }

            $selector = ".listActivity a.small[id='".$createdActivityId."']";
            $browser->waitUsing(20, 200, function () use ($browser, $selector): bool {
                $exists = $browser->script(
                    "return document.querySelector(".json_encode($selector).") !== null;"
                )[0] ?? false;

                return (bool) $exists;
            }, 'No s\'ha afegit el + dinàmic de l\'activitat book.');

            $plusClicked = $browser->script(
                "const sel = ".json_encode($selector).";".
                "if (typeof window.jQuery === 'undefined') { return false; }".
                "const el = window.jQuery(sel);".
                "if (!el.length) { return false; }".
                "el.trigger('click'); return true;"
            )[0] ?? false;

            $this->assertTrue((bool) $plusClicked, 'No s\'ha pogut clicar el + dinàmic.');

            $browser->waitUsing(10, 200, function () use ($browser, $comment): bool {
                $value = $browser->script("return document.querySelector('#dialogo #explicacion')?.value || ''; ")[0] ?? '';
                return (string) $value === $comment;
            }, 'El modal no ha carregat el comentari de l\'activitat dinàmica.');

            $loadedValue = (string) ($browser->script(
                "return document.querySelector('#dialogo #explicacion')?.value || '';"
            )[0] ?? '');
            $this->assertSame($comment, $loadedValue);
        });
    }

    /**
     * Verifica que poble i empresa es combinen i que buidar empresa conserva el poble.
     */
    public function test_filtra_per_empresa_i_poble_en_la_mateixa_pestana(): void
    {
        $profesor = $this->profesorAmbColaboracionsOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $browser->visit('/profesor/login')
                ->type('codigo', $login['identifier'])
                ->type('password', $login['password'])
                ->press('Entra')
                ->pause(1500)
                ->visit('/misColaboraciones')
                ->pause(1800)
                ->assertPresent('.mis-colaboraciones-company-filter');

            $result = $browser->script(<<<'JS'
const normalizeCompany = (value) => (value || '')
  .toString()
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, '')
  .toUpperCase()
  .replace(/[^A-Z0-9\s]/g, ' ')
  .replace(/\s+/g, ' ')
  .trim();
const normalizeTown = (value) => (value || '')
  .toString()
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, '')
  .toUpperCase()
  .replace(/[0-9]/g, ' ')
  .replace(/Y/g, 'I')
  .replace(/[^A-Z\s]/g, ' ')
  .replace(/\s+/g, ' ')
  .trim();
const visible = (element) => element && element.offsetParent !== null;
const target = Array.from(document.querySelectorAll('.mis-colaboraciones-card')).find(visible);

if (!target) {
  return {ok: false, reason: 'No hi ha targetes visibles'};
}

const targetTab = target.getAttribute('data-target-tab') || '';
const company = normalizeCompany(target.getAttribute('data-company') || '');
const town = normalizeTown(target.getAttribute('data-town') || '');
const companyFilter = document.querySelector('.mis-colaboraciones-company-filter[data-target-tab="' + targetTab + '"]');
const townFilter = document.querySelector('.mis-colaboraciones-town-filter[data-target-tab="' + targetTab + '"]');

if (!companyFilter || !townFilter || !company || !town) {
  return {ok: false, reason: 'Falten dades o filtres'};
}

const companyQuery = company.slice(0, Math.max(1, Math.ceil(company.length / 2)));
companyFilter.value = companyQuery;
companyFilter.dispatchEvent(new Event('input', {bubbles: true}));

const cardsForTab = Array.from(document.querySelectorAll('.mis-colaboraciones-card[data-target-tab="' + targetTab + '"]'));
const companyMatches = cardsForTab.filter(visible);
const companyOk = companyMatches.length > 0 && companyMatches.every((card) => {
  return normalizeCompany(card.getAttribute('data-company') || '').includes(companyQuery);
});

townFilter.value = target.getAttribute('data-town') || '';
townFilter.dispatchEvent(new Event('input', {bubbles: true}));
const combinedMatches = cardsForTab.filter(visible);
const combinedOk = combinedMatches.length > 0 && combinedMatches.every((card) => {
  return normalizeCompany(card.getAttribute('data-company') || '').includes(companyQuery)
    && normalizeTown(card.getAttribute('data-town') || '').includes(town);
});

companyFilter.value = '';
companyFilter.dispatchEvent(new Event('input', {bubbles: true}));
const townMatches = cardsForTab.filter(visible);
const clearOk = townMatches.length > 0 && townMatches.every((card) => {
  return normalizeTown(card.getAttribute('data-town') || '').includes(town);
});

return {
  ok: companyOk && combinedOk && clearOk,
  companyMatches: companyMatches.length,
  combinedMatches: combinedMatches.length,
  townMatches: townMatches.length
};
JS
            )[0] ?? ['ok' => false, 'reason' => 'Sense resultat JavaScript'];

            $this->assertTrue((bool) ($result['ok'] ?? false), json_encode($result));
        });
    }

    /** Verifica les classificacions interactives del panell departamental. */
    public function test_classifica_les_colaboracions_del_departament(): void
    {
        $profesor = $this->profesorAmbColaboracionsOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $browser->visit('/profesor/login')
                ->type('codigo', $login['identifier'])
                ->type('password', $login['password'])
                ->press('Entra')
                ->pause(1500)
                ->visit('/colaboracion')
                ->pause(1800)
                ->assertPresent('#colaboracion-department-panel')
                ->assertPresent('.colaboracion-department-card');

            $result = $browser->script(<<<'JS'
const container = document.querySelector('#colaboracion-department-groups');
const allCards = Array.from(container.querySelectorAll('.colaboracion-department-card'));
const views = ['town', 'cycle', 'contacted', 'collaborates', 'documentation', 'inactive'];
const attributes = {
  contacted: 'data-contacted',
  collaborates: 'data-collaborates',
  documentation: 'data-documentation-old',
  inactive: 'data-inactive'
};

if (!allCards.length) {
  return {ok: false, reason: 'No hi ha fitxes departamentals'};
}

const checks = {};
for (const view of views) {
  const button = document.querySelector('.js-colaboracion-view[data-view="' + view + '"]');
  if (!button) {
    return {ok: false, reason: 'Falta la vista ' + view};
  }

  button.click();
  const rendered = Array.from(container.querySelectorAll('.colaboracion-department-card'));
  const expected = attributes[view]
    ? allCards.filter((card) => card.getAttribute(attributes[view]) === '1')
    : allCards;

  checks[view] = rendered.length === expected.length
    && rendered.every((card) => !attributes[view] || card.getAttribute(attributes[view]) === '1')
    && button.getAttribute('aria-pressed') === 'true';
}

document.querySelector('.js-colaboracion-view[data-view="town"]').click();
const restored = container.querySelectorAll('.colaboracion-department-card').length === allCards.length;

return {
  ok: Object.values(checks).every(Boolean) && restored,
  checks,
  total: allCards.length
};
JS
            )[0] ?? ['ok' => false, 'reason' => 'Sense resultat JavaScript'];

            $this->assertTrue((bool) ($result['ok'] ?? false), json_encode($result));
        });
    }

    /**
     * Selecciona un professor amb emailItaca i col·laboracions en el panell.
     */
    private function profesorAmbColaboracionsOrSkip(): ?Profesor
    {
        try {
            /** @var ColaboracionService $service */
            $service = app(ColaboracionService::class);

            $candidats = Profesor::query()
                ->where('activo', 1)
                ->whereNotNull('emailItaca')
                ->where('emailItaca', '!=', '')
                ->get();

            foreach ($candidats as $profesor) {
                if ($service->panelListingByTutor((string) $profesor->dni)->isNotEmpty()) {
                    return $profesor;
                }
            }
        } catch (Throwable $exception) {
            $this->markTestSkipped('Dusk sense connexió DB operativa: '.$exception->getMessage());
            return null;
        }

        $this->markTestSkipped('No hi ha professor amb col·laboracions disponibles per a provar el panell en Dusk.');
        return null;
    }

    /**
     * Retorna l'id de l'última activitat "book" creada per un professor.
     */
    private function bookActivityIdByCommentForProfesor(string $dni, string $comment): ?int
    {
        try {
            $id = \Intranet\Entities\Activity::query()
                ->where('action', 'book')
                ->where('author_id', $dni)
                ->where('comentari', $comment)
                ->latest('id')
                ->value('id');
        } catch (Throwable $exception) {
            return null;
        }

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * Prepara credencials estables per a login web en Dusk.
     *
     * @return array{identifier:string,password:string}
     */
    private function prepareProfesorForUiLogin(Profesor $profesor): array
    {
        $plainPassword = 'DuskPass_2026';
        $identifier = trim((string) ($profesor->email ?? ''));

        if ($identifier === '') {
            $identifier = 'dusk.'.strtolower((string) $profesor->dni).'@test.local';
            $profesor->email = $identifier;
        }

        $profesor->password = bcrypt($plainPassword);
        $profesor->changePassword = (string) ($profesor->changePassword ?: now()->toDateString());

        if ((int) ($profesor->activo ?? 0) !== 1) {
            $profesor->activo = 1;
        }

        if (empty($profesor->emailItaca)) {
            $profesor->emailItaca = $identifier;
        }

        if (!is_string($profesor->remember_token) || $profesor->remember_token === '') {
            $profesor->remember_token = Str::random(20);
        }

        $profesor->save();

        return [
            'identifier' => $identifier,
            'password' => $plainPassword,
        ];
    }
}
