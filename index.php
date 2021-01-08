<?php

/* 
Author:  Jakub Jalowiec
E-mail:  kuba.jalowiec@protonmail.com
Website: https://github.com/kubajal/module-safe_file_upload
This file is part of Safe File Upload - a Formtools module.

Safe File Upload is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

Safe File Upload is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Safe File Upload.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("../../global/library.php");
$module = FormTools\Modules::initModulePage("admin");

$module->displayPage("index.tpl");
