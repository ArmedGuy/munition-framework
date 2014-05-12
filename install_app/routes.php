<?php
$r->get(MUNITION_WEBPATH, "install#home");
$r->get(MUNITION_WEBPATH . "verify_rewrite", "install#verify_rewrite");

$r->error("404", "install#home");
