<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get specific users
        $admin = User::where('email', 'admin@example.com')->first();
        $testUser = User::where('email', 'test@example.com')->first();
        $demo = User::where('email', 'demo@example.com')->first();

        // Create forms for Admin User (12 forms)
        if ($admin) {
            $this->createFormsForUser($admin, 12, 'admin');
        }

        // Create forms for Test User (8 forms)
        if ($testUser) {
            $this->createFormsForUser($testUser, 8, 'test');
        }

        // Create forms for Demo User (6 forms)
        if ($demo) {
            $this->createFormsForUser($demo, 6, 'demo');
        }
    }

    private function createFieldsForForm(Form $form, array $fields): void
    {
        foreach ($fields as $fieldData) {
            $configuration = [
                'type' => $fieldData['type'],
                'name' => $fieldData['name'],
                'label' => ['en' => $fieldData['label']],
                'required' => $fieldData['required'] ?? false,
            ];

            // Add placeholder for text-type fields
            if (isset($fieldData['placeholder']) && in_array($fieldData['type'], ['text', 'email', 'password', 'number', 'textarea', 'tel', 'search', 'url'])) {
                $configuration['placeholder'] = ['en' => $fieldData['placeholder']];
            }

            // Add options for select, radio, checkbox fields
            if (isset($fieldData['options']) && in_array($fieldData['type'], ['select', 'radio', 'checkbox'])) {
                $configuration['options'] = array_map(function ($option) {
                    return ['value' => $option, 'label' => ['en' => $option]];
                }, $fieldData['options']);
            }

            $field = $form->fields()->create([
                'type' => $fieldData['type'],
                'order' => $fieldData['order'],
                'configuration' => $configuration,
            ]);
        }
    }

    private function createFormsForUser(User $user, int $count, string $type): void
    {
        for ($i = 0; $i < $count; $i++) {
            $formData = $this->getFormData($i, $type);

            $form = Form::create([
                'user_id' => $user->id,
                'name' => $formData['name'],
                'description' => $formData['description'],
                'is_active' => $formData['is_active'],
                'configuration' => $formData['configuration'],
            ]);

            // Create fields for this form
            $this->createFieldsForForm($form, $formData['fields']);
        }
    }

    private function getFormData(int $index, string $type): array
    {
        $forms = [
            [
                'name' => ['en' => 'Customer Feedback Survey'],
                'description' => ['en' => 'Collect valuable feedback from customers to improve our services'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Full Name', 'label' => 'Full Name', 'required' => true, 'placeholder' => 'Enter your full name'],
                    ['type' => 'email', 'order' => 2, 'name' => 'Email Address', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'Enter your email address'],
                    ['type' => 'select', 'order' => 3, 'name' => 'Satisfaction Level', 'label' => 'How satisfied are you with our service?', 'required' => true, 'options' => ['Very Satisfied', 'Satisfied', 'Neutral', 'Dissatisfied', 'Very Dissatisfied']],
                    ['type' => 'textarea', 'order' => 4, 'name' => 'Feedback', 'label' => 'Please share your feedback', 'required' => false, 'placeholder' => 'Tell us what you think about our service'],
                    ['type' => 'number', 'order' => 5, 'name' => 'Rating', 'label' => 'Rate our service (1-10)', 'required' => true, 'placeholder' => 'Enter a number between 1 and 10'],
                ],
            ],
            [
                'name' => ['en' => 'Job Application Form'],
                'description' => ['en' => 'Professional job application form for potential candidates'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'First Name', 'label' => 'First Name', 'required' => true, 'placeholder' => 'Enter your first name'],
                    ['type' => 'text', 'order' => 2, 'name' => 'Last Name', 'label' => 'Last Name', 'required' => true, 'placeholder' => 'Enter your last name'],
                    ['type' => 'email', 'order' => 3, 'name' => 'Email', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'Enter your email address'],
                    ['type' => 'tel', 'order' => 4, 'name' => 'Phone', 'label' => 'Phone Number', 'required' => true, 'placeholder' => 'Enter your phone number'],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Experience', 'label' => 'Work Experience', 'required' => true, 'placeholder' => 'Describe your relevant work experience'],
                    ['type' => 'file', 'order' => 6, 'name' => 'Resume', 'label' => 'Upload Resume', 'required' => true],
                    ['type' => 'select', 'order' => 7, 'name' => 'Position', 'label' => 'Position Applied For', 'required' => true, 'options' => ['Software Developer', 'Designer', 'Marketing Manager', 'Sales Representative', 'Customer Support']],
                ],
            ],
            [
                'name' => ['en' => 'Event Registration'],
                'description' => ['en' => 'Register for upcoming events and workshops'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Full Name', 'label' => 'Full Name', 'required' => true, 'placeholder' => 'Enter your full name'],
                    ['type' => 'email', 'order' => 2, 'name' => 'Email', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'Enter your email address'],
                    ['type' => 'select', 'order' => 3, 'name' => 'Event Type', 'label' => 'Event Type', 'required' => true, 'options' => ['Workshop', 'Conference', 'Seminar', 'Training', 'Networking']],
                    ['type' => 'date', 'order' => 4, 'name' => 'Preferred Date', 'label' => 'Preferred Date', 'required' => false],
                    ['type' => 'checkbox', 'order' => 5, 'name' => 'Newsletter', 'label' => 'Subscribe to newsletter', 'required' => false],
                    ['type' => 'textarea', 'order' => 6, 'name' => 'Special Requirements', 'label' => 'Special Requirements', 'required' => false, 'placeholder' => 'Any special requirements or requests'],
                ],
            ],
            [
                'name' => ['en' => 'Product Review Form'],
                'description' => ['en' => 'Share your experience with our products'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Product Name', 'label' => 'Product Name', 'required' => true, 'placeholder' => 'Enter the product name'],
                    ['type' => 'select', 'order' => 2, 'name' => 'Category', 'label' => 'Product Category', 'required' => true, 'options' => ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Other']],
                    ['type' => 'number', 'order' => 3, 'name' => 'Rating', 'label' => 'Product Rating (1-5)', 'required' => true, 'placeholder' => 'Rate from 1 to 5'],
                    ['type' => 'textarea', 'order' => 4, 'name' => 'Review', 'label' => 'Your Review', 'required' => true, 'placeholder' => 'Share your thoughts about the product'],
                    ['type' => 'checkbox', 'order' => 5, 'name' => 'Recommend', 'label' => 'Would you recommend this product?', 'required' => false],
                    ['type' => 'email', 'order' => 6, 'name' => 'Email', 'label' => 'Email (for follow-up)', 'required' => false, 'placeholder' => 'Enter your email if you want us to follow up'],
                ],
            ],
            [
                'name' => ['en' => 'Contact Support Form'],
                'description' => ['en' => 'Get help from our support team'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Name', 'label' => 'Your Name', 'required' => true, 'placeholder' => 'Enter your name'],
                    ['type' => 'email', 'order' => 2, 'name' => 'Email', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'Enter your email address'],
                    ['type' => 'select', 'order' => 3, 'name' => 'Issue Type', 'label' => 'Issue Type', 'required' => true, 'options' => ['Technical Problem', 'Billing Issue', 'Feature Request', 'General Inquiry', 'Bug Report']],
                    ['type' => 'select', 'order' => 4, 'name' => 'Priority', 'label' => 'Priority Level', 'required' => true, 'options' => ['Low', 'Medium', 'High', 'Critical']],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Description', 'label' => 'Issue Description', 'required' => true, 'placeholder' => 'Please describe your issue in detail'],
                    ['type' => 'file', 'order' => 6, 'name' => 'Screenshot', 'label' => 'Upload Screenshot (optional)', 'required' => false],
                ],
            ],
            [
                'name' => ['en' => 'Survey Form'],
                'description' => ['en' => 'General survey to gather user insights'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Age Group', 'label' => 'Age Group', 'required' => true, 'placeholder' => 'Enter your age'],
                    ['type' => 'select', 'order' => 2, 'name' => 'Gender', 'label' => 'Gender', 'required' => false, 'options' => ['Male', 'Female', 'Other', 'Prefer not to say']],
                    ['type' => 'select', 'order' => 3, 'name' => 'Occupation', 'label' => 'Occupation', 'required' => false, 'options' => ['Student', 'Employee', 'Self-employed', 'Unemployed', 'Retired']],
                    ['type' => 'radio', 'order' => 4, 'name' => 'Internet Usage', 'label' => 'How often do you use the internet?', 'required' => true, 'options' => ['Daily', 'Weekly', 'Monthly', 'Rarely']],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Opinion', 'label' => 'Your Opinion', 'required' => false, 'placeholder' => 'Share your thoughts on the topic'],
                ],
            ],
            // Additional forms for admin and test users
            [
                'name' => ['en' => 'Employee Onboarding'],
                'description' => ['en' => 'Complete employee onboarding process'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Employee ID', 'label' => 'Employee ID', 'required' => true, 'placeholder' => 'Enter your employee ID'],
                    ['type' => 'text', 'order' => 2, 'name' => 'Full Name', 'label' => 'Full Name', 'required' => true, 'placeholder' => 'Enter your full name'],
                    ['type' => 'email', 'order' => 3, 'name' => 'Work Email', 'label' => 'Work Email', 'required' => true, 'placeholder' => 'Enter your work email'],
                    ['type' => 'select', 'order' => 4, 'name' => 'Department', 'label' => 'Department', 'required' => true, 'options' => ['IT', 'HR', 'Marketing', 'Sales', 'Finance', 'Operations']],
                    ['type' => 'date', 'order' => 5, 'name' => 'Start Date', 'label' => 'Start Date', 'required' => true],
                    ['type' => 'file', 'order' => 6, 'name' => 'Documents', 'label' => 'Upload Required Documents', 'required' => true],
                    ['type' => 'checkbox', 'order' => 7, 'name' => 'Agreements', 'label' => 'I agree to company policies', 'required' => true],
                ],
            ],
            [
                'name' => ['en' => 'Project Proposal'],
                'description' => ['en' => 'Submit project proposal for review'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Project Title', 'label' => 'Project Title', 'required' => true, 'placeholder' => 'Enter project title'],
                    ['type' => 'textarea', 'order' => 2, 'name' => 'Description', 'label' => 'Project Description', 'required' => true, 'placeholder' => 'Describe your project'],
                    ['type' => 'number', 'order' => 3, 'name' => 'Budget', 'label' => 'Estimated Budget', 'required' => true, 'placeholder' => 'Enter estimated budget'],
                    ['type' => 'select', 'order' => 4, 'name' => 'Priority', 'label' => 'Priority Level', 'required' => true, 'options' => ['Low', 'Medium', 'High', 'Critical']],
                    ['type' => 'date', 'order' => 5, 'name' => 'Deadline', 'label' => 'Project Deadline', 'required' => true],
                    ['type' => 'file', 'order' => 6, 'name' => 'Proposal', 'label' => 'Upload Proposal Document', 'required' => true],
                ],
            ],
            // Additional forms for admin only
            [
                'name' => ['en' => 'System Configuration'],
                'description' => ['en' => 'Configure system settings and preferences'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'select', 'order' => 1, 'name' => 'Environment', 'label' => 'Environment', 'required' => true, 'options' => ['Development', 'Staging', 'Production']],
                    ['type' => 'text', 'order' => 2, 'name' => 'Database URL', 'label' => 'Database URL', 'required' => true, 'placeholder' => 'Enter database connection string'],
                    ['type' => 'checkbox', 'order' => 3, 'name' => 'Debug Mode', 'label' => 'Enable Debug Mode', 'required' => false],
                    ['type' => 'number', 'order' => 4, 'name' => 'Timeout', 'label' => 'Request Timeout (seconds)', 'required' => true, 'placeholder' => 'Enter timeout value'],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Notes', 'label' => 'Configuration Notes', 'required' => false, 'placeholder' => 'Additional notes'],
                ],
            ],
            [
                'name' => ['en' => 'User Management'],
                'description' => ['en' => 'Manage user accounts and permissions'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Username', 'label' => 'Username', 'required' => true, 'placeholder' => 'Enter username'],
                    ['type' => 'email', 'order' => 2, 'name' => 'Email', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'Enter email address'],
                    ['type' => 'select', 'order' => 3, 'name' => 'Role', 'label' => 'User Role', 'required' => true, 'options' => ['Admin', 'Manager', 'User', 'Guest']],
                    ['type' => 'checkbox', 'order' => 4, 'name' => 'Active', 'label' => 'Account Active', 'required' => false],
                    ['type' => 'date', 'order' => 5, 'name' => 'Expiry Date', 'label' => 'Account Expiry Date', 'required' => false],
                ],
            ],
            [
                'name' => ['en' => 'Performance Metrics'],
                'description' => ['en' => 'Track and analyze system performance'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'date', 'order' => 1, 'name' => 'Report Date', 'label' => 'Report Date', 'required' => true],
                    ['type' => 'number', 'order' => 2, 'name' => 'Response Time', 'label' => 'Average Response Time (ms)', 'required' => true, 'placeholder' => 'Enter response time'],
                    ['type' => 'number', 'order' => 3, 'name' => 'Throughput', 'label' => 'Requests per Second', 'required' => true, 'placeholder' => 'Enter throughput'],
                    ['type' => 'select', 'order' => 4, 'name' => 'Status', 'label' => 'System Status', 'required' => true, 'options' => ['Excellent', 'Good', 'Fair', 'Poor', 'Critical']],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Issues', 'label' => 'Identified Issues', 'required' => false, 'placeholder' => 'List any issues found'],
                ],
            ],
            [
                'name' => ['en' => 'Security Audit'],
                'description' => ['en' => 'Conduct security audit and assessment'],
                'is_active' => true,
                'configuration' => ['locales' => ['en']],
                'fields' => [
                    ['type' => 'text', 'order' => 1, 'name' => 'Auditor', 'label' => 'Auditor Name', 'required' => true, 'placeholder' => 'Enter auditor name'],
                    ['type' => 'date', 'order' => 2, 'name' => 'Audit Date', 'label' => 'Audit Date', 'required' => true],
                    ['type' => 'select', 'order' => 3, 'name' => 'Risk Level', 'label' => 'Risk Level', 'required' => true, 'options' => ['Low', 'Medium', 'High', 'Critical']],
                    ['type' => 'textarea', 'order' => 4, 'name' => 'Findings', 'label' => 'Security Findings', 'required' => true, 'placeholder' => 'Describe security findings'],
                    ['type' => 'textarea', 'order' => 5, 'name' => 'Recommendations', 'label' => 'Recommendations', 'required' => true, 'placeholder' => 'List security recommendations'],
                    ['type' => 'file', 'order' => 6, 'name' => 'Report', 'label' => 'Upload Audit Report', 'required' => true],
                ],
            ],
        ];

        // Return different forms based on user type and index
        if ($type === 'admin') {
            // Admin gets all forms (12 total)
            return $forms[$index % count($forms)];
        }
        if ($type === 'test') {
            // Test user gets first 8 forms
            $testForms = array_slice($forms, 0, 8);

            return $testForms[$index % count($testForms)];
        }
        // Demo user gets first 6 forms
        $demoForms = array_slice($forms, 0, 6);

        return $demoForms[$index % count($demoForms)];
    }
}
