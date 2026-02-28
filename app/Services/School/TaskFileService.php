<?php

declare(strict_types=1);

namespace Intranet\Services\School;

use Illuminate\Http\UploadedFile;
use Intranet\Entities\Task;
use Intranet\Services\UI\AppAlert as Alert;

class TaskFileService
{
    public function store(UploadedFile $file, Task $task): ?string
    {
        if (!$file->isValid()) {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return null;
        }

        return $file->storeAs(
            'Eventos',
            str_shuffle('abcdefgh123456') . '.' . $file->getClientOriginalExtension(),
            'public'
        );
    }
}

