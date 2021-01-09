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
use FormTools\Core;
use FormTools\ListGroups;

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

    protected $group_name = "safe_file_upload_cf";
    protected $group_setting_name = "safe_file_upload_field_group_id";
    protected $module_name = "safe_file_upload";

    protected $nav = array(
        "module_name" => array("index.php", false)
    );

    private function getGroupId() {
      $db = Core::$db;
      $query = "SELECT setting_value FROM {PREFIX}settings WHERE setting_name = :setting_name AND module = :module";
      $db->query($query);
      $db->bind("setting_name", $this->group_setting_name);
      $db->bind("module", $this->module_name);
      $db->execute();
      $row = $db->fetch();
      return $row["setting_value"];
    }
    
    public function install($module_id) {

		if (!Modules::checkModuleEnabled("custom_fields")) {
            $L = $this->getLangStrings();
            return array(false, $L["cf_requirement_not_fulfiled"]);
        }

        // create a new group of fields in Custom Fields
        $info = ListGroups::addListGroup('field_types', $this->group_name);

        // save the created group's ID as a setting of this module
        $group_id = $info["group_id"];
        $query = "INSERT INTO {PREFIX}settings (setting_name, setting_value, module) VALUES (:setting_name, :setting_value, :module)";
        $db = Core::$db;
        $db->query($query);
        $db->bind("setting_name", $this->group_setting_name);
        $db->bind("setting_value", $group_id);
        $db->bind("module", $this->module_name);
        $db->execute();
        return array(true, "");
    }

    public function uninstall($module_id) {
        $db = Core::$db;
        $group_id = $this->getGroupId();
        ListGroups::deleteListGroup($group_id);

        // remember that all settings are removed in global/code/Modules.class.php anyway

        return array(true, "");
    }
}
