<?php

Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});

require __DIR__.'/auth.php';
require __DIR__.'/log-viewer.php';

Breadcrumbs::for('admin.connected_users', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});


Breadcrumbs::for('admin.connected_users_by_country', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});

Breadcrumbs::for('admin.connected_users_per_server_by_country', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});


Breadcrumbs::for('admin.subscriptions.index', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});




//admin.v-p-n-servers.index

Breadcrumbs::for('admin.v-p-n-servers.index', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});

//admin.v-p-n-servers.show

Breadcrumbs::for('admin.v-p-n-servers.show', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});


// admin.subscription-radius-attibutes.index
Breadcrumbs::for('admin.subscription-radius-attributes.index', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});
Breadcrumbs::for('admin.subscription-radius-attributes.edit', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});
Breadcrumbs::for('admin.subscription-radius-attributes.create', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});
Breadcrumbs::for('admin.subscription-radius-attributes.show', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.subscriptions.index'));
});


