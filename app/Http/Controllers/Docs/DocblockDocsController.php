<?php

namespace Intranet\Http\Controllers\Docs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intranet\Http\Controllers\Controller;

/**
 * Renderitza la documentacio de doc-blocks de l'aplicacio.
 */
class DocblockDocsController extends Controller
{
    /**
     * Mostra la pagina de documentacio amb index lateral de seccions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $path = base_path('docs/app-docblocks-index.md');

        if (!File::exists($path)) {
            abort(404, 'No s\'ha trobat el fitxer de documentacio de doc-blocks.');
        }

        $markdown = File::get($path);
        $content = Str::markdown($markdown);
        $sections = [];
        $sectionIndex = 0;

        $content = preg_replace_callback(
            '/<h2>(.*?)<\/h2>/i',
            function (array $matches) use (&$sections, &$sectionIndex): string {
                $title = trim(strip_tags($matches[1]));
                $id = 'section-'.(++$sectionIndex).'-'.Str::slug($title);
                $sections[] = [
                    'id' => $id,
                    'title' => $title,
                ];

                return '<h2 id="'.e($id).'">'.$matches[1].'</h2>';
            },
            $content
        ) ?? $content;

        return response()
            ->view('docs.app-docblocks', [
                'content' => $content,
                'sections' => $sections,
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
