<?php
$r->get("/", "test#index");

$r->error("404", "test#not_found");

$r->get("/test_filters1", "test#test_filters1");
$r->get("/test_filters2", "test#test_filters2");
