@if ($tipus === \Intranet\Entities\AssumpteParticularMailDelivery::URGENT)
    <p>La persona {{ $nomProfessor }} ha presentat una petició urgent d’assumptes particulars per al {{ $dataGaudi }}.</p>
    <p>Motivació excepcional: {{ $motivacio }}</p>
@elseif ($tipus === \Intranet\Entities\AssumpteParticularMailDelivery::DENEGADA)
    <p>La teua petició d’assumptes particulars per al {{ $dataGaudi }} ha sigut denegada.</p>
    <p>Motiu: {{ $motiuDenegacio }}</p>
@elseif ($tipus === \Intranet\Entities\AssumpteParticularMailDelivery::AUTORITZADA)
    <p>La teua petició d’assumptes particulars per al {{ $dataGaudi }} ha sigut autoritzada.</p>
    <p>Pots consultar la resolució firmada des de la intranet: <a href="{{ $urlDocument }}">Obrir la resolució</a>.</p>
@endif
