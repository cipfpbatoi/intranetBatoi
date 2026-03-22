<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Doc-blocks de l'aplicacio</title>
    <style>
        :root {
            --bg: #f7f9fc;
            --surface: #ffffff;
            --text: #232f3e;
            --line: #dbe3ea;
            --brand: #85ea2d;
            --brand-dark: #1b1f24;
            --title: #6fbe44;
            --section-bg: #effbe6;
            --section-border: #8fd45f;
            --file-title: #0f766e;
            --class-title: #166534;
            --class-code-bg: #ecfdf3;
            --class-code-border: #b7ebc6;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font: 15px/1.6 "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .topbar {
            background: var(--brand-dark);
            color: #fff;
            border-top: 4px solid var(--brand);
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .topbar a {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 13px;
        }
        .topbar-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .wrapper {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 16px;
        }
        .layout {
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }
        .toc-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            padding: 14px;
            position: sticky;
            top: 16px;
        }
        .toc { font-size: 14px; }
        .toc h2 {
            margin: 0 0 10px 0;
            padding: 0;
            border: 0;
            background: transparent;
            color: #14532d;
            font-size: 16px;
        }
        .toc ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .toc li + li { margin-top: 6px; }
        .toc a {
            display: block;
            text-decoration: none;
            color: #14532d;
            background: #f5fdee;
            border: 1px solid #d1efb8;
            border-radius: 6px;
            padding: 6px 8px;
        }
        .toc a:hover { background: #ebfad9; }
        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        h1, h2, h3, h4 {
            color: #111827;
            line-height: 1.3;
            margin-top: 1.2em;
            margin-bottom: 0.6em;
        }
        h1 { margin-top: 0; border-bottom: 1px solid var(--line); padding-bottom: 10px; color: var(--title); }
        h2 {
            color: #14532d;
            background: var(--section-bg);
            border-left: 5px solid var(--section-border);
            border-radius: 6px;
            padding: 8px 10px;
        }
        h3 {
            color: var(--file-title);
            border-bottom: 1px dashed #b7d8d2;
            padding-bottom: 6px;
        }
        h4 {
            color: var(--class-title);
            margin-top: 0.9em;
        }
        p, li { color: var(--text); }
        a { color: #1976d2; }
        code {
            background: #f2f5f8;
            border: 1px solid #e3eaf1;
            border-radius: 4px;
            padding: 0.1rem 0.35rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 0.92em;
        }
        pre {
            background: #0f172a;
            color: #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
            border: 1px solid #1e293b;
        }
        pre code {
            background: transparent;
            border: 0;
            color: inherit;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid var(--line);
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f8fbff;
            color: #334155;
            font-weight: 600;
        }
        hr { border: 0; border-top: 1px solid var(--line); margin: 18px 0; }
        h3 code,
        h4 code {
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            border-width: 1px;
        }
        h3 code {
            background: #e9f8f5;
            border-color: #b8e5dc;
            color: #0f766e;
        }
        h4 code {
            background: var(--class-code-bg);
            border-color: var(--class-code-border);
            color: #166534;
        }
        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }
            .toc-panel {
                position: static;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <strong>Intranet API Docs</strong>
        <div class="topbar-links">
            <a href="/api/documentation">Obrir Swagger</a>
            <a href="{{ route('docs.bbdd-esquema') }}">Esquema BBDD</a>
        </div>
    </header>
    <main class="wrapper">
        <div class="layout">
            <aside class="toc-panel">
                <nav class="toc" aria-label="Index de seccions">
                    <h2>Index</h2>
                    <ul>
                        @foreach ($sections as $section)
                            <li><a href="#{{ e($section['id']) }}">{{ e($section['title']) }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            </aside>
            <section class="panel">{!! $content !!}</section>
        </div>
    </main>
</body>
</html>
