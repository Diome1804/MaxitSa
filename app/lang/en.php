<?php

return [
    // Validation messages
    'validation' => [
        'required' => 'This field is required',
        'email_invalid' => 'Invalid email address',
        'password_invalid' => 'Password must contain at least 8 characters, one uppercase, one lowercase, one digit and one special character',
        'phone_invalid' => 'Invalid phone number',
        'cni_invalid' => 'Invalid CNI number',
        'min_length' => 'This field is too short',
        'date_invalid' => 'Invalid date',
        'amount_invalid' => 'Invalid amount',
        'amount_positive' => 'Amount must be greater than 0',
    ],

    // Authentication messages
    'auth' => [
        'login_required' => 'You must be logged in to access this page',
        'login_success' => 'Login successful',
        'login_failed' => 'Invalid credentials',
        'logout_success' => 'Logout successful',
        'access_denied' => 'Access denied',
    ],

    // Account messages
    'account' => [
        'create_success' => 'Secondary account created successfully',
        'create_failed' => 'Error creating account',
        'insufficient_balance' => 'Insufficient balance in main account',
        'update_failed' => 'Error updating main account',
        'not_found' => 'Account not found or unauthorized',
        'already_main' => 'This account is already the main account',
        'no_main_account' => 'No main account found',
        'change_main_success' => 'Main account changed successfully',
        'change_main_failed' => 'Error changing main account',
        'id_required' => 'Account ID missing',
        'id_invalid' => 'Invalid account ID',
        'phone_required' => 'Phone number required',
        'amount_required' => 'Amount required',
        'balance_initial_positive' => 'Initial balance must be greater than 0',
    ],

    // Transaction messages
    'transaction' => [
        'date_range_invalid' => 'Start date must be before end date',
        'filter_applied' => 'Filters applied',
        'no_transactions' => 'No transactions found',
        'error_loading' => 'Error loading transactions',
    ],

    // General messages
    'general' => [
        'form_error' => 'Please correct form errors',
        'server_error' => 'Internal server error',
        'success' => 'Operation successful',
        'error' => 'An error occurred',
        'invalid_request' => 'Invalid request',
        'access_forbidden' => 'Access forbidden',
    ],
];
