<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GX_Text_Deactivator {

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
