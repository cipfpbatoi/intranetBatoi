@foreach($elemento->getFillable() as $property)
    @php $tipo = $default[$property]['type']; @endphp
    {!! Field::$tipo($property,$default[$property]['default'],['id'=>$property,'wire:model'=>$property]) !!}
@endforeach
