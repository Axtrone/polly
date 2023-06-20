<?php
include_once('data/storage.php');
class UserStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('data/users.json'));
    }
  }
?>