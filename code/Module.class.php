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
use FormTools\Modules\CustomFields\FieldTypes as CustomFieldTypes;
use FormTools\FieldTypes as CoreFieldTypes;
use Exception;

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

    protected $group_name = "Safe File Upload field group (do not delete!)";
    protected $group_setting_name = "safe_file_upload_field_group_id";
    protected $field_setting_name = "safe_file_upload_field_field_id";
    protected $field_name = "Safe File Upload";
    protected $field_type_identifier = "safe_file_upload";
    protected $module_name = "safe_file_upload";
    protected $safe_uploads_dir = __DIR__ . "/../safe_uploads";
    protected $tableName = "module_safe_file_uploads";

    protected $nav = array(
        "module_name" => array("index.php", false)
    );

    public function handleUpload($vars) {
      $file_field_name = $vars["field_info"]["field_name"];
      $file_name = $_FILES[$file_field_name]["name"];
      $tmp_path = $_FILES[$file_field_name]["tmp_name"];
      $path_info = pathinfo($file_name);
      $extension = $path_info['extension'];
      $hash = substr(md5(openssl_random_pseudo_bytes(20)),-32);
      $new_path = $this->safe_uploads_dir . "/" . $hash . "." . $extension;
      $success  = @copy($tmp_path, $new_path);
      if(!$success)
      {
        throw new Exception("Something went wrong during copying the uploaded file to the safe uploads directory.");
      }

      $value = $file_name . "::" . $hash . "." . $extension;
    }

    protected function formatMessage($message_template, $values_array) {
      
      $message = $message_template;

      foreach($values_array as $key => $value) {
        $message = str_replace($key, $value, $message);
      }
      return $message;
    } 

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

    private function getFieldId() {
      $db = Core::$db;
      $query = "SELECT setting_value FROM {PREFIX}settings WHERE setting_name = :setting_name AND module = :module";
      $db->query($query);
      $db->bind("setting_name", $this->field_setting_name);
      $db->bind("module", $this->module_name);
      $db->execute();
      $row = $db->fetch();
      return $row["setting_value"];
    }

    private function getFTFFieldTypeId() {
      $L = $this->getLangStrings();

      $ftf_module_id = Modules::getModuleIdFromModuleFolder("field_type_file");
      if($ftf_module_id == "") {
        // did not find Field Type File module
        // this code is after checks if that module is enabled
        // something is seriously wrong
        throw new Exception($L["sfu_requirement_not_fulfiled_ftf"]);
      }
      $db = Core::$db;
      $query = "SELECT field_type_id FROM {PREFIX}field_types WHERE field_type_identifier = :field_type_identifier AND managed_by_module_id = :module_id";
      $db->query($query);
      $db->bind("field_type_identifier", "file");
      $db->bind("module_id", $ftf_module_id);
      $db->execute();
      $row = $db->fetch();
      return $row["field_type_id"];
    }
    
    public function install($module_id) {

        $L = $this->getLangStrings();
		    if (!Modules::checkModuleEnabled("custom_fields")) {
            $L = $this->getLangStrings();
            return array(false, $L["sfu_requirement_not_fulfiled_cf"]);
        }
		    if (!Modules::checkModuleEnabled("field_type_file")) {
            $L = $this->getLangStrings();
            return array(false, $L["sfu_requirement_not_fulfiled_ftf"]);
        }

        Modules::instantiateModule("custom_fields");
 
        $db = Core::$db;
        $db->beginTransaction();

        try {
          $charset = Core::getDbTableCharset();

          // create a new table that stores info about uploads
          $db->query("
              CREATE TABLE {PREFIX}$this->tableName (
                upload_id mediumint(8) unsigned NOT NULL auto_increment,
                account_id mediumint(8) unsigned NOT NULL,
                form_id mediumint(8) unsigned NOT NULL,
                file_hash varchar(255) NOT NULL,
                orignal_file_name varchar(255) NOT NULL,
                PRIMARY KEY (upload_id)
              ) ENGINE=InnoDB DEFAULT CHARSET=$charset
          ");
          $db->execute();

          // create a new group of fields in Custom Fields
          $info = ListGroups::addListGroup('field_types', $this->group_name);
  
          // save the created group's ID as a setting of this module
          $group_id = $info["group_id"];
          $query = "INSERT INTO {PREFIX}settings (setting_name, setting_value, module) VALUES (:setting_name, :setting_value, :module)";
          $db->query($query);
          $db->bind("setting_name", $this->group_setting_name);
          $db->bind("setting_value", $group_id);
          $db->bind("module", $this->module_name);
          $db->execute();
  
          // add a new field type for safe file uploads
          $original_file_type_id = $this->getFTFFieldTypeId();
          $request = [
            "action" => "add_field",
            "field_type_name" => $this->field_name,
            "field_type_identifier" => $this->field_name,
            "group_id" => "" . $group_id,
            "original_field_type_id" => "" . $original_file_type_id
          ];
          $field_type_id = CustomFieldTypes::addFieldType($request);
          $query = "INSERT INTO {PREFIX}settings (setting_name, setting_value, module) VALUES (:setting_name, :setting_value, :module)";
          $db = Core::$db;
          $db->query($query);
          $db->bind("setting_name", $this->field_setting_name);
          $db->bind("setting_value", $field_type_id);
          $db->bind("module", $this->module_name);
          $db->execute();
          $db->processTransaction();
        } catch (Exception $e) {
          $db->rollbackTransaction();
          return array(false, $this->formatMessage($L["sfu_installation_failed"], ["{error}" => $e->getMessage()]));
      }

        return array(true, "");
    }

    public function uninstall($module_id) {

      Modules::instantiateModule("custom_fields");

      $L = $this->getLangStrings();
      $db = Core::$db;
      $db->beginTransaction();
      try {
        // delete table that stores info about uploaded files
        $db->query("DROP TABLE {PREFIX}$this->tableName");
        $db->execute();
        
        // delete the safe file upload field and replace all its occurances with 'textarea'
        $field_id = $this->getFieldId();
        $field_delete_info = CustomFieldTypes::deleteFieldType($field_id, $L);

        $group_id = $this->getGroupId();
        ListGroups::deleteListGroup($group_id);
        // remember that all settings are removed in global/code/Modules.class.php anyway

        $db->processTransaction();
        return array(true, "");
      } catch (Exception $e) {
        $db->rollbackTransaction();
        return array(false, $this->formatMessage($L["sfu_uninstallation_failed"], ["{error}" => $e->getMessage()]));
      }
      return array(true, "");
  }
}
