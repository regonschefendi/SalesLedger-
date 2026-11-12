@include('partials.profile-edit-form', [
    'title' => 'Edit Profile Sales - Sales Ledger',
    'action' => route('sales.profile.update'),
    'backUrl' => route('sales.profile.index'),
])
