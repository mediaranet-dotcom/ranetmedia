<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the billing system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Allow Invoice for Inactive Customers
    |--------------------------------------------------------------------------
    |
    | This option controls whether invoices can be generated for customers
    | with status other than 'active'. Set to true to allow invoicing
    | inactive customers, false to restrict to active customers only.
    |
    */
    'allow_inactive_customer_invoice' => false,

    /*
    |--------------------------------------------------------------------------
    | Auto Invoice Generation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic invoice generation
    |
    */
    'auto_generation' => [
        // Only generate for services with these statuses
        'allowed_service_statuses' => ['active'],

        // Only generate for customers with these statuses (if enabled)
        'allowed_customer_statuses' => ['active'],

        // Check customer status in auto generation
        'check_customer_status' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Status Configuration
    |--------------------------------------------------------------------------
    |
    | Define which invoice statuses are considered as pending/unpaid
    |
    */
    'pending_statuses' => [
        'draft',
        'sent',
        'pending',
        'overdue',
        'partial_paid'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default values for invoice generation
    |
    */
    'defaults' => [
        'due_days' => 7, // Default due days if not set in billing cycle
        'tax_rate' => 0.11, // Default PPN 11%
        'currency' => 'IDR',
        'currency_symbol' => 'Rp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Number Format
    |--------------------------------------------------------------------------
    |
    | Format for generating invoice numbers
    |
    */
    'invoice_number_format' => 'INV-{year}{month}-{sequence}',
    'invoice_number_sequence_length' => 4,

    /*
    |--------------------------------------------------------------------------
    | PDF Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF generation
    |
    */
    'pdf' => [
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'margin' => [
            'top' => 20,
            'right' => 20,
            'bottom' => 20,
            'left' => 20,
        ],
    ],
];
