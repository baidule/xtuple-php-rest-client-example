<?php

global $apiConfig;
$apiConfig = array(
  // True if objects should be returned by the service classes.
  // False if associative arrays should be returned (default behavior).
  'use_objects' => true, // Set to true for this example.

  // The application_name is included in the User-Agent HTTP header.
  'application_name' => 'xTuple REST API PHP Example',

  // OAuth2 Settings, you can get these keys at from the Mobile Client
  // Admin Interface when the OAuth 2.0 service account client was created.
  'oauth2_client_id' => 'your-xtuple-client-id-here', // Set your client id here.
  'oauth2_client_secret' => '', // Can leave blank for this example using JWT.
  'oauth2_redirect_uri' => '', // Can leave blank for this example using JWT.
  'oauth2_token_uri' => 'https://your-xtuple-mobile-host-here/your-database-name-here/oauth/token', // Set your host here.
  'oauth2_auth_url' => '', // Can leave blank for this example using JWT.
  'oauth2_federated_signon_certs_url' => '', // Can leave blank for this example using JWT.

  // xTuple OAuth 2.0 Authentication class to use.
  'authClass' => 'rescued_OAuth2', // Do not change for this example. See: rescued_oauth2.class.inc

  // A special development or testing environment host.
  'basePath' => 'https://your-xtuple-mobile-host-here', // Set your host here.

  // Definition of service specific values like scopes, oauth token URLs, etc
  'services' => array(
    'contact' => array(
      'scope' => array(
        'https://your-xtuple-mobile-host-here/your-database-name-here/auth/contact', // Set your host here.
      )
    )
  ),

  // xTuple specific config settings.
  'xtuple' => array(
    'url' => 'https://your-xtuple-mobile-host-here/your-database-name-here/discovery/v1alpha1/apis/v1alpha1/rest', // Set your host here.
    'rescued_debug_mode' => true,
    'oauth2_deligate' => 'admin',
    'oauth2_pk12_filename' => 'privatekey.p12', // The PK12 file downloaded from xTuple's "OAUTH2" client registration interface.
    'oauth2_pk12_pass' => 'notasecret', // Do not need to change this.
    'oauth2_scope' => 'https://your-xtuple-mobile-host-here/your-database-name-here/auth/contact', // Set your host here.
  ),
);
