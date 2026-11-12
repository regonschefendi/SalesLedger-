@include('partials.profile-edit-form', [
    'title' => 'Edit Profile - Sales Ledger',
    'action' => route('admin.profile.update'),
    'backUrl' => route('admin.profile.index'),
])
