<?php

Route::resource('/fichar', 'FicharController', ['except' => ['destroy', 'update']]);

