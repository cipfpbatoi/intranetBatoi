<?php

declare(strict_types=1);

namespace Intranet\Application\Menu;

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
     * Construeix l'estructura de menú a partir dels registres actius.
     *
     * @param string $nom
     * @param object $user
     * @return array<string, mixed>
     */
    private function build(string $nom, object $user): array
    {
        $menu = [];

        $itemsQuery = Menu::query()
            ->where('menu', '=', $nom)
            ->where('activo', '=', 1)
            ->orderBy('submenu')
            ->orderBy('orden');

        if ($this->isAdminUser($user)) {
            $itemsQuery->where('rol', '<>', 5);
        } else {
            $itemsQuery->whereIn('rol', rolesUser($user->rol));
        }

        $items = $itemsQuery->get();
        $submenus = $items->where('submenu', '')->values();
        $childrenBySubmenu = $items->where('submenu', '!=', '')->groupBy('submenu');

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
}
