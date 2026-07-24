<?php

require_once "../repositories/RoleRepository.php";

$roleRepository = new RoleRepository();

$result = $roleRepository->deleteRole(2);

if ($result) {
    echo "Role Deleted Successfully!";
} else {
    echo "Role Delete Failed!";
}