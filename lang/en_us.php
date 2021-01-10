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

$L = array();

// required fields
$L["module_name"] = "Safe File Upload";
$L["module_description"] = "Store files uploaded in forms in a non-public directory. See more in the <b><a href=\"https://github.com/kubajal/module-safe_file_upload\"> git repo </a></b>.";

// custom fields
$L["sfu_requirement_not_fulfiled_cf"] = "Module you are trying to install depends on the Custom Fields module which appears to be missing or is disabled.";
$L["sfu_requirement_not_fulfiled_ftf"] = "Module you are trying to install depends on the File Type Field module which appears to be missing or is disabled.";
$L["sfu_installation_failed"] = "Error during installation: {error}. Use <a href=\"https://github.com/kubajal/module-safe_file_upload/issues\">the git repo</a> to report any problems.";
$L["sfu_uninstallation_failed"] = "Error during uninstallation: {error}. Use <a href=\"https://github.com/kubajal/module-safe_file_upload/issues\">the git repo</a> to report any problems.";
$L["notify_field_type_deleted"] = "Safe Upload File field deleted succefully.";
$L["notify_cannot_delete_invalid_field_type_id"] = "Failed to delete Safe Upload File field.";