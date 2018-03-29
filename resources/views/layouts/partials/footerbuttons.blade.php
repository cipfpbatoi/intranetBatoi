<!-- /menu footer buttons -->
<div class="sidebar-footer hidden-small">
    <a data-toggle="tooltip" data-placement="top" title="Settings">
        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </a>
    @if (!isset(AuthUser()->nia))
        <a data-toggle="tooltip" data-placement="top" title="Enviar codigo fichaje" href='myApiToken'>
            <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Logout" href="/logout">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
        </a>
    @else
        <a data-toggle="tooltip" data-placement="top" title="Lock">
            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Logout" href="/alumno/logout">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
        </a>
    @endif
</div>
<!-- /menu footer buttons -->