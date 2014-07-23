<?php

/**
 * ##################################
 * Setup this app and the REST Client
 * ##################################
 */
require_once (dirname(__FILE__)  . '/google-api-php-client/src/Google_Client.php');
require_once (dirname(__FILE__)  . '/json-patch-php/JsonPatch.inc');
require_once (dirname(__FILE__)  . '/rescued_api.class.inc');
require_once (dirname(__FILE__)  . '/rescued_oauth2.class.inc');

// If a local configuration file is found, merge it's values with the default configuration.
// xTuple specific settings in 'local_config.php' allows Google library will work with xTuple API.
if (file_exists(dirname(__FILE__)  . '/local_config.php')) {
  $defaultConfig = $apiConfig;
  require_once (dirname(__FILE__)  . '/local_config.php');
  $apiConfig = array_merge($defaultConfig, $apiConfig);
}

// Make sure you keep your PK12 key file in a secure location, and isn't readable by others.
$key_file = $apiConfig['xtuple']['oauth2_pk12_filename'];

$client = new Google_Client();

// TODO - Remove for production.
// Disabling SSL vert verificaiton for local dev testing on self signed cert.
$client::$io->setOptions(array (CURLOPT_SSL_VERIFYPEER => !$apiConfig['xtuple']['rescued_debug_mode']));

// Set your cached access token. Remember to replace $_SESSION with a
// real database or memcached.
session_start();
//session_unset();
if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
}

// Load the key in PKCS 12 format (you need to download this from the Mobile
// Client Admin Interface when the OAuth 2.0 service account client was created.
$key = file_get_contents($key_file);
$oauth = new Google_AssertionCredentials(
  $apiConfig['oauth2_client_id'],
  array($apiConfig['xtuple']['oauth2_scope']),
  $key,
  $apiConfig['xtuple']['oauth2_pk12_pass'],
  'assertion',
  false
);

// Set OAuth 2.0 JWT Delegated user.
$oauth->prn = $apiConfig['xtuple']['oauth2_deligate'];
$client->setAssertionCredentials($oauth);

// Create a new service client.
$service = new Rescued_ApiService($client, $apiConfig['xtuple']);

echo "<html>
        <head>
          <link rel='stylesheet' href='style.css' />
        </head>
        <body class='html not-front one-sidebar sidebar-first'>
        <div class='column sidebar' id='sidebar-first'><p>&nbsp;</p></div>
        <div class='column' id='content'>";

// Below are some very basic DELETE, GET, PATCH and POST request examples.
// These examples are intended to show you how to interact with xTuple's
// REST API using Google's PHP API Client library and the JSON-Patch library.
//
// WARNING: These examples are in no way secure or using best practices
// for PHP application. This is meant for educational purposes only.
//
// There is a lot more that could be done to expand on these examples.
//
// TODO: Create a basic user interface.
// TODO: Change POST into an add form that creates fields from the JSON-Schema.
// TODO: Change PATCH into an edit form that creates fields from the JSON-Schema.

/**
 * ########################
 * List Request of contacts
 * ########################
 *
 * Example: http://localhost/example/index.php
 */
if (!isset($_GET['method']) && !isset($_GET['id'])) {
  $key = get_resource_key_field($service, $apiConfig, 'Contact');
  $added =  false;
  $output = '';

  // Make the GET request.
  $result = $service->Contact->list();
  foreach ($result['data'] as $value) {
    $output .= "<tr>
            <td>";
    $output .= "<a href='index.php?method=GET&id=" . $value[$key] . "'>" . $value[$key] . "</a>";
    $output .= "  </td>";
    $output .= "  <td>";
    $output .= $value['firstName'];
    $output .= "  </td>";
    $output .= "  <td>";
    $output .= $value['lastName'];
    $output .= "  </td>";
    $output .= "  <td>";
    $output .= $value['phone'];
    $output .= "  </td>";
    $output .= "  <td>";
    $output .= $value['primaryEmail'];
    $output .= "  </td>";
    $output .= "  <td>";
    $output .= "<a href='index.php?method=GET&id=" . $value[$key] . "'> View </a>";
    $output .= " - <a href='index.php?method=PATCH&id=" . $value[$key] . "'> Add Comment </a>";
    if ($value[$key] === '42') {
      $output .= " - <a href='index.php?method=DELETE&id=" . $value[$key] . "'> Delete </a>";
    }
    $output .= "  </td>
          </tr>";

    if ($value[$key] === '42') {
      $added = true;
    }
  }

  echo "<h2>List of Contacts:</h2>";

  if (!$added) {
    echo "<ul class='action-links'><li><a href='index.php?method=POST'> + Add Contact 42</a></li></ul>";
  }

  echo "<pre>";
 // print_r($result);
  echo "</pre>";

  // Create table.
  echo "<table>
          <thead>
              <tr>
                <th>Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>
          </thead>
          <tbody>
            ";
  echo $output;

  echo "</tbody>
      </table>";
}

/**
 * #################
 * DELETE Request
 * #################
 *
 * Example: http://localhost/example/index.php?method=DELETE&id=42
 */
if ($_GET['method'] === 'DELETE') {
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $key = get_resource_key_field($service, $apiConfig, 'Contact');

  // Make the DELETE request.
  if ($id === '42') {
    $result = $service->Contact->delete($id, $key);

    echo "<a href='index.php'>Return to List</a>";
    echo "<h2>Contact DELETE number $id:</h2>";
    echo "<h2>Contact DELETE Result:</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
  } else {
    echo "<a href='index.php'>Return to List</a>";
    echo "<h2>Only Contact 42 can be deleted in this example.</h2>";
  }
}

/**
 * #################
 * GET Request
 * #################
 *
 * Example: http://localhost/example/index.php?method=GET&id=1
 */
if ($_GET['method'] === 'GET') {
  $id = isset($_GET['id']) ? $_GET['id'] : '42';
  $key = get_resource_key_field($service, $apiConfig, 'Contact');

  // Make the GET request.
  $result = $service->Contact->get($id, $key);

  echo "<a href='index.php'>Return to List</a>";
  if ($value[$key] === '42') {
    echo " <a href='index.php?method=DELETE&id=" . $result->data[$key] . "'>Delete</a>";
  }
  echo " <a href='index.php?method=PATCH&id=" . $result->data[$key] . "'>Add a Comment</a>";
  echo "<h2>Contact GET number $id:</h2>";
  echo "<h2>Contact GET Result:</h2>";
  echo "<pre>";
  print_r($result);
  echo "</pre>";
}

/**
 * #################
 * POST Request
 * #################
 *
 * Example: http://localhost/example/index.php?method=POST
 */
if ($_GET['method'] === 'POST') {

  // Build a contact object to POST.
  $contact = '{
    "number":"42",
    "isActive":true,
    "honorific":"Mr",
    "firstName":"John",
    "middleName":"",
    "lastName":"Doe",
    "suffix":"",
    "jobTitle":"",
    "initials":"",
    "phone":"111-342-5657",
    "alternate":"",
    "fax":"111-342-1100",
    "primaryEmail":"test@prodiem.com",
    "webAddress":"",
    "account":null,
    "owner":null,
    "notes":null,
    "address":null,
    "comments":[

    ],
    "characteristics":[

    ],
    "accounts":[

    ],
    "contacts":[

    ],
    "items":[

    ],
    "files":[

    ],
    "urls":[

    ],
    "incidents":[

    ],
    "opportunities":[

    ],
    "toDos":[

    ],
    "incidentRelations":[

    ],
    "opportunityRelations":[

    ],
    "toDoRelations":[

    ],
    "projects":[

    ],
    "projectRelations":[

    ],
    "customers":[

    ]
  }';

  // Create a service object that will be the body of the request.
  $requestBody = new Rescued_Service(json_decode($contact, true));

  // Make the POST request.
  $result = $service->Contact->insert($requestBody);

  echo "<a href='index.php'>Return to List</a>";
  echo "<h2>Contact POST Object:</h2>";
  echo "<pre>";
  print_r($requestBody);
  echo "</pre>";

  echo "<h2>Contact POST Result:</h2>";
  echo "<pre>";
  print_r($result);
  echo "</pre>";
}

/**
 * #################
 * PATCH Request
 * #################
 *
 * Example: http://localhost/example/index.php?method=PATCH&id=42
 */
if ($_GET['method'] === 'PATCH') {
  $id = isset($_GET['id']) ? $_GET['id'] : '42';
  $key = get_resource_key_field($service, $apiConfig, 'Contact');

  // Get the contact to do some work on.
  $contact = $service->Contact->get($id, $key);

  echo "<a href='index.php'>Return to List</a>";
  echo '<h2>Contact Pre-PATCH Object:</h2>';
  echo "<pre>";
  print_r($contact);
  echo '</pre>';

  $patch = '{
    "op":"add",
    "path":"\/comments\/0",
    "value":{
      "commentType":"General",
      "text":"test",
      "isPublic":false,
      "created":"2013-06-14T18:50:21.904Z",
      "createdBy":"admin"
    }
  }';

  echo '<h2>Contact JSON-Patch Object:</h2>';
  echo "<pre>";
  print_r($patch);
  echo '</pre>';

  // Use JSON-Patch library to apply the patch to our contact.
  $patched = JsonPatch::patch(json_encode($contact->data), $patch);

  echo '<h2>Contact PATCHed Object:</h2>';
  echo "<pre>";
  print_r($patched);
  echo '</pre>';

  // A PATCH request body expects an array key of 'patches' with patch objects.
  $patches = array(
    'patches' => array(
      json_decode($patch)
    ),
  );

  // Create a service object that will be the body of the request.
  $requestBody = new Rescued_Service($patches);

  // Set the etag from contact.
  $requestBody->setEtag($contact->getEtag());

  // Make the PATCH request.
  // TODO: get 'number' key from discovery doc.
  $result = $service->Contact->patch($id, 'number', $requestBody);

  echo "<h2>Contact PATCH Request Response:</h2>";
  echo "<pre>";
  print_r($result);
  echo "</pre>";

  // Apply the response patches to are already patched contact.
  $patchedResponse = JsonPatch::patch(json_encode($patched), $result->patches);

  // This is a diff from the original contact GET and contact after applying
  // the patched in the request response.
  $diff = JsonPatch::diff(json_encode($contact->data), json_encode($patchedResponse));
  echo '<h2>Diff Result:</h2>';
  echo "<pre>";
  print_r($diff);
  echo '</pre>';

  echo '<h2>Final Contact PATCHed Object:</h2>';
  echo "<pre>";
  print_r($patchedResponse);
  echo '</pre>';
}


echo "    </div>
        </body>
      </html>";

// We're not done yet. Remember to update the cached access token.
// Remember to replace $_SESSION with a real database or memcached.
if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();
}

// Helper functoin to discover to key field for a resource from the JSON-Schema.
function get_resource_key_field($service, $apiConfig, $resource) {
  $discovery = $service->getDiscovery($apiConfig['xtuple']['url']);

  foreach ($discovery['schemas'][$resource]['properties'] as $property => $schema) {
    if (isset($schema['isKey']) && $schema['isKey']) {
      $key = $property;
    }
  }

  return $key;
}
