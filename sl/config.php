<?php

class Config
{
    public static $site_title = 'StickLib';
    public static $site_api_url = '';
    public static $site_base_url = '';
    public static $site_logo_url = '';
    public static $site_description = 'StickLib ServerApis';

    public static $sv_use_cors = true;
    public static $sv_use_https = true;
    public static $sv_use_cache = false;
    public static $sv_use_oauth = true;
    public static $sv_use_scripts = true;
    public static $sv_header_string = 'type: application/js; \n';

    public static $sv_server_type = 'apache';
    public static $sv_server_version = '2.1';
    public static $sv_server_route = '/var/www/html';

    public static $file_allow_upload = false;
    public static $file_local_path = 'public/assets';
    public static $file_use_s3 = false;
    public static $file_use_edge_cache = false;
    public static $file_use_native_naming = true;

    public static $aws_access_key_id = '';
    public static $aws_access_key_sc = '';
    public static $aws_access_api_rg = '';
    public static $aws_bucket_s3_name = '';

    public static $db_host = '';
    public static $db_user = '';
    public static $db_pass = '';
    public static $db_port = '';
    public static $db_base = '';
}