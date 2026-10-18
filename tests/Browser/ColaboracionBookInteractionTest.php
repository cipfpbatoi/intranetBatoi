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

    /** Verifica que els filtres departamentals es combinen sobre una fitxa per empresa. */
    public function test_combina_els_filtres_de_colaboracions_del_departament(): void
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
                ->assertPresent('#colaboracion-town-filter')
                ->assertPresent('#colaboracion-cycle-filter')
                ->assertPresent('#colaboracion-state-filter')
                ->assertPresent('#colaboracion-assignment-filter')
                ->assertPresent('#colaboracion-documentation-filter')
                ->assertPresent('.colaboracion-company-card')
                ->assertPresent('.colaboracion-cycle-row');

            $result = $browser->script(<<<'JS'
const grid = document.querySelector('#colaboracion-company-grid');
const cards = Array.from(grid.querySelectorAll('.colaboracion-company-card'));
const rows = Array.from(grid.querySelectorAll('.colaboracion-cycle-row'));
const townFilter = document.querySelector('#colaboracion-town-filter');
const cycleFilter = document.querySelector('#colaboracion-cycle-filter');
const stateFilter = document.querySelector('#colaboracion-state-filter');
const assignmentFilter = document.querySelector('#colaboracion-assignment-filter');
const documentationFilter = document.querySelector('#colaboracion-documentation-filter');
const clearFilters = document.querySelector('#colaboracion-clear-filters');
const companiesOutput = document.querySelector('#colaboracion-visible-companies');
const collaborationsOutput = document.querySelector('#colaboracion-visible-collaborations');

if (!cards.length || !rows.length || !townFilter || !cycleFilter || !stateFilter
  || !assignmentFilter || !documentationFilter || !clearFilters
  || !companiesOutput || !collaborationsOutput) {
  return {ok: false, reason: 'Falten fitxes o controls del panell departamental'};
}

const companyKeys = cards.map((card) => card.getAttribute('data-company-key'));
const uniqueCompanies = companyKeys.length === new Set(companyKeys).size;
const visibleRows = () => rows.filter((row) => {
  const card = row.closest('.colaboracion-company-card');
  return !row.hidden && card && !card.hidden;
});
const visibleCards = () => cards.filter((card) => !card.hidden);

documentationFilter.click();
const documentationRows = visibleRows();
const documentationOk = documentationFilter.getAttribute('aria-pressed') === 'true'
  && documentationRows.every((row) => row.getAttribute('data-documentation-current') === '1');
documentationFilter.click();

const target = rows[0];
const targetState = target.getAttribute('data-state') || '0';
townFilter.value = target.getAttribute('data-town') || '';
cycleFilter.value = target.getAttribute('data-cycle') || '';
stateFilter.value = targetState === '0'
  ? 'no-contact'
  : (targetState === '2' ? 'collaborates' : 'contacted');
assignmentFilter.value = target.getAttribute('data-assigned') === '1' ? 'assigned' : 'unassigned';

[townFilter, cycleFilter, stateFilter, assignmentFilter].forEach((filter) => {
  filter.dispatchEvent(new Event('change', {bubbles: true}));
});

const filteredRows = visibleRows();
const cumulativeOk = filteredRows.length > 0 && filteredRows.every((row) => {
  const state = row.getAttribute('data-state') || '0';
  const stateMatches = stateFilter.value === 'no-contact'
    ? state === '0'
    : (stateFilter.value === 'collaborates' ? state === '2' : state !== '0');

  return row.getAttribute('data-town') === townFilter.value
    && row.getAttribute('data-cycle') === cycleFilter.value
    && row.getAttribute('data-assigned') === target.getAttribute('data-assigned')
    && stateMatches;
});
const groupedOk = visibleCards().every((card) => {
  return Array.from(card.querySelectorAll('.colaboracion-cycle-row')).some((row) => !row.hidden);
});
const countersOk = Number(companiesOutput.textContent) === visibleCards().length
  && Number(collaborationsOutput.textContent) === filteredRows.length;

clearFilters.click();
const restored = visibleRows().length === rows.length
  && visibleCards().length === cards.length
  && documentationFilter.getAttribute('aria-pressed') === 'false';

return {
  ok: uniqueCompanies && documentationOk && cumulativeOk && groupedOk && countersOk && restored,
  uniqueCompanies,
  documentationOk,
  cumulativeOk,
  groupedOk,
  countersOk,
  restored,
  companies: cards.length,
  collaborations: rows.length
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
