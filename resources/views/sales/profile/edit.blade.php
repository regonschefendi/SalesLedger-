@include('partials.profile-edit-form', [
    'title' => 'Ubah Profil Sales - Sales Ledger',
    'action' => route('sales.profile.update'),
    'backUrl' => route('sales.profile.index'),
])
