<?php
/**
 * PayMongo configuration.
 *
 * Replace the placeholder values below with your actual PayMongo API keys.
 * Get your keys at: https://dashboard.paymongo.com/developers
 *
 * Use TEST keys during development and LIVE keys only on production.
 */

// Your PayMongo Secret Key (server-side only — never expose this in HTML/JS)
define('PAYMONGO_SECRET_KEY', 'sk_test_REPLACE_WITH_YOUR_SECRET_KEY');

// Your PayMongo Public Key (used in webhook signature verification)
define('PAYMONGO_PUBLIC_KEY', 'pk_test_REPLACE_WITH_YOUR_PUBLIC_KEY');

// Webhook signing secret (found in PayMongo Dashboard → Webhooks → your endpoint)
define('PAYMONGO_WEBHOOK_SECRET', 'whsk_REPLACE_WITH_YOUR_WEBHOOK_SECRET');

// Base URL of your site (used to build success/failure redirect URLs)
define('SITE_BASE_URL', 'http://localhost/enrollna');
