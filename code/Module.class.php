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

namespace FormTools\Modules\SafeFileUpload;

use FormTools\Module as FormToolsModule;
use FormTools\Modules;

class Module extends FormToolsModule
{
    protected $moduleName = "Safe File Upload";
    protected $moduleDesc = "Store files uploaded in forms in a non-public directory.";
    protected $author = "Jakub Jalowiec";
    protected $authorEmail = "kuba.jalowiec@protonmail.com";
    protected $authorLink = "https://github.com/kubajal";
    protected $version = "0.0.1";
    protected $date = "2021-01-08";
    protected $originLanguage = "en_us";

    protected $nav = array(
        "module_name" => array("index.php", false)
    );
    
    public function install($module_id) {

		if (!Modules::checkModuleEnabled("custom_fields")) {
            $L = $this->getLangStrings();
            return array(false, $L["cf_requirement_not_fulfiled"]);
        }
        return array(true, "");
    }

    public function uninstall($module_id) {
        return array(true, "");
    }
}
