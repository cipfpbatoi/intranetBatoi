<?php

declare(strict_types=1);

namespace Intranet\Application\Menu;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intranet\Entities\Menu;
use Styde\Html\Facades\Menu as StydeMenu;

/**
 * Servei d'aplicació per construir i cachejar menús de navegació.
 *
 * Responsabilitats:
 * - generar l'arbre de menú (amb filtres de rol),
 * - encapsular cache per usuari/menú,
 * - invalidar cache després de canvis CRUD.
 */
class MenuService
{
    private const CACHE_KEYS_INDEX = 'menu:keys:index';

    /**
     * Construeix el menú per nom i usuari.
     *
     * Si no hi ha usuari autenticat, retorna menú buit.
     *
     * @param string $nom Nom funcional del menú (p. ex. `general`, `topmenu`).
     * @param bool $array Si és `true`, retorna array; si és `false`, objecte renderitzable StydeMenu.
     * @param object|null $user Usuari explícit per tests o contextos no HTTP.
     * @return mixed
     */
    public function make(string $nom, bool $array = false, $user = null)
    {
        $user = $user ?? authUser();
        if (!$user) {
            return $array ? [] : StydeMenu::make([]);
        }

        $cacheKey = $this->cacheKey($nom, (string) $user->dni);
        $menu = Cache::remember($cacheKey, now()->addDay(), function () use ($nom, $user, $cacheKey) {
            $this->registerCacheKey($cacheKey);
            return $this->build($nom, $user);
        });

        if ($array) {
            return $menu;
        }

        return StydeMenu::make($menu);
    }

    /**
     * Neteja el cache de menú (global o filtrat per nom/dni).
     *
     * @param string|null $nom
     * @param string|null $dni
     */
    public function clearCache(?string $nom = null, ?string $dni = null): void
    {
        $keys = Cache::get(self::CACHE_KEYS_INDEX, []);
        if (!is_array($keys) || $keys === []) {
            return;
        }

        $toForget = array_filter($keys, function (string $key) use ($nom, $dni): bool {
            if ($nom !== null && !str_contains($key, ':' . $nom . ':')) {
                return false;
            }
            if ($dni !== null && !str_ends_with($key, ':' . $dni)) {
                return false;
            }
            return true;
        });

        foreach ($toForget as $key) {
            Cache::forget($key);
        }

        $remaining = array_values(array_diff($keys, $toForget));
        Cache::forever(self::CACHE_KEYS_INDEX, $remaining);
    }

    /**
     * Retorna menús ordenats per al grid, normalitzant ordres abans.
     */
    public function listForGrid()
    {
        $this->sortForGrid();
        return Menu::all();
    }

    /**
     * Persisteix un menú des del request.
     */
    public function saveFromRequest(Request $request, $id = null): Menu
    {
        $elemento = $id ? Menu::findOrFail($id) : new Menu();
        $elemento->fillAll($request);
        $elemento->save();
        $this->clearCache();

        return $elemento;
    }

    /**
     * Duplica un menú dins del mateix grup/submenú.
     */
    public function copy(int|string $id): Menu
    {
        $elemento = Menu::findOrFail($id);
        $copia = new Menu();
        $copia->fill($elemento->toArray());
        $copia->orden = Menu::where('menu', $elemento->menu)
            ->where('submenu', $elemento->submenu)
            ->max('orden') + 1;
        $copia->activo = false;
        $copia->save();
        $this->clearCache();

        return $copia;
    }

    /**
     * Mou un menú cap amunt dins del bloc actual.
     */
    public function moveUp(int|string $id): void
    {
        $elemento = Menu::findOrFail($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;

        while (!$find && $orden > 1) {
            $find = Menu::where('orden', --$orden)
                ->where('menu', $elemento->menu)
                ->where('submenu', $elemento->submenu)
                ->first();
        }

        if ($find) {
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
            $this->clearCache();
        }
    }

    /**
     * Mou un menú cap avall dins del bloc actual.
     */
    public function moveDown(int|string $id): void
    {
        $elemento = Menu::findOrFail($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;

        while (!$find && $orden < 100) {
            $find = Menu::where('orden', ++$orden)
                ->where('menu', $elemento->menu)
                ->where('submenu', $elemento->submenu)
                ->first();
        }

        if ($find) {
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
            $this->clearCache();
        }
    }

    /**
     * Construeix l'estructura de menú a partir dels registres actius.
     *
     * @param string $nom
     * @param object $user
     * @return array<string, mixed>
     */
    private function build(string $nom, object $user): array
    {
        $menu = [];

        // Manté comportament legacy: filtrem per rol només en nivell pare.
        $parentsQuery = Menu::query()
            ->where('menu', '=', $nom)
            ->where('submenu', '=', '')
            ->where('activo', '=', 1)
            ->orderBy('orden');

        if ($this->isAdminUser($user)) {
            $parentsQuery->where('rol', '<>', 5);
        } else {
            $parentsQuery->whereIn('rol', rolesUser($user->rol));
        }

        $submenus = $parentsQuery->get();

        $childrenBySubmenu = Menu::query()
            ->where('menu', '=', $nom)
            ->where('activo', '=', 1)
            ->where('submenu', '!=', '')
            ->orderBy('orden')
            ->get()
            ->groupBy('submenu');

        foreach ($submenus as $sitem) {
            if ((string) $sitem->url === '') {
                $menu[$sitem->nombre]['class'] = $sitem->class;
                foreach ($childrenBySubmenu->get($sitem->nombre, []) as $item) {
                    $menu[$sitem->nombre]['submenu'][$item->nombre] = [
                        $this->tipoUrl($item->url) => $item->url,
                        'img' => $item->img,
                        'roles' => $item->rol,
                        'secure' => true,
                    ];
                }
            } else {
                $menu[$sitem->nombre] = [
                    $this->tipoUrl($sitem->url) => $sitem->url,
                    'class' => $sitem->class,
                    'secure' => true,
                ];
            }
        }

        return $menu;
    }

    /**
     * Determina si una URL és externa o interna per a StydeMenu.
     */
    private function tipoUrl($url): string
    {
        $url = trim((string) $url);
        return preg_match('#^(https?:)?//#i', $url) === 1 ? 'full-url' : 'url';
    }

    /**
     * Compon la clau de cache per menú i usuari.
     */
    private function cacheKey(string $nom, string $dni): string
    {
        return "menu:$nom:$dni";
    }

    /**
     * Registra la clau en l'índex global per permetre invalidació selectiva.
     */
    private function registerCacheKey(string $key): void
    {
        $keys = Cache::get(self::CACHE_KEYS_INDEX, []);
        if (!is_array($keys)) {
            $keys = [];
        }

        if (!in_array($key, $keys, true)) {
            $keys[] = $key;
            Cache::forever(self::CACHE_KEYS_INDEX, $keys);
        }
    }

    /**
     * Comprovació local de rol admin sobre l'usuari rebut.
     */
    private function isAdminUser(object $user): bool
    {
        return isset($user->rol) && esRol((int) $user->rol, 11);
    }

    /**
     * Reordena pares i fills per mantindre seqüència contínua.
     */
    private function sortForGrid(): void
    {
        $anterior = '';
        foreach (Menu::where('submenu', '')->orderBy('menu')->orderBy('orden')->get() as $menu) {
            if ($anterior != $menu->menu) {
                $orden = 1;
                $anterior = $menu->menu;
            }
            if ((int) $menu->orden !== $orden) {
                $menu->orden = $orden;
                $menu->save();
            }
            $orden++;
        }

        foreach (Menu::where('submenu', '')->orderBy('menu')->orderBy('orden')->get() as $menu) {
            $orden = 1;
            foreach (Menu::where('submenu', $menu->nombre)->orderBy('orden')->get() as $submenu) {
                if ((int) $submenu->orden !== $orden) {
                    $submenu->orden = $orden;
                    $submenu->save();
                }
                $orden++;
            }
        }
    }
}
