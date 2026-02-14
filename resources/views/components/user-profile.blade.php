<li>
    <a href="javascript:;">
        <i class="fa fa-envelope "></i>
        {{ $usuario->email }}
    </a>
</li>
@isset($usuario->Departamento->vliteral)
    <li>
        <a href="javascript:;">
            <i class="fa fa-briefcase "></i>
            {{ $usuario->Departamento->vliteral }}
        </a>
    </li>
 @endisset


