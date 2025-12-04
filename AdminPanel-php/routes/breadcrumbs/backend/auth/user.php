<?php

Breadcrumbs::for('admin.auth.user.index', function ($trail) {
    $trail->push(__('labels.backend.access.users.management'), route('admin.auth.user.index'));
});


Breadcrumbs::for('admin.auth.user.search', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('labels.backend.access.users.management'), route('admin.auth.user.index'));
});

Breadcrumbs::for('admin.auth.user.deactivated', function ($trail) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.deactivated'), route('admin.auth.user.deactivated'));
});

Breadcrumbs::for('admin.auth.user.deleted', function ($trail) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.deleted'), route('admin.auth.user.deleted'));
});

Breadcrumbs::for('admin.auth.user.create', function ($trail) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('labels.backend.access.users.create'), route('admin.auth.user.create'));
});

Breadcrumbs::for('admin.auth.user.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.view'), route('admin.auth.user.show', $id));
});

Breadcrumbs::for('admin.auth.user.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});

Breadcrumbs::for('admin.auth.user.change-password', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.change-password'), route('admin.auth.user.change-password', $id));
});


// User Subscriptions

//admin.auth.user-subscriptions.index

Breadcrumbs::for('admin.auth.user-subscriptions.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscriptions.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscriptions.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscriptions.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
//admin.auth.user-servers.index

Breadcrumbs::for('admin.auth.user-servers.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-servers.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-servers.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-servers.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});

// admin.auth.user-subscription-extensions.index

Breadcrumbs::for('admin.auth.user-subscription-extensions.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscription-extensions.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscription-extensions.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-subscription-extensions.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});

//admin.auth.user-radius-attributes.index

Breadcrumbs::for('admin.auth.user-radius-attributes.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-radius-attributes.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-radius-attributes.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-radius-attributes.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});


//admin.auth.user-rad-acct.index

Breadcrumbs::for('admin.auth.user-rad-acct.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-rad-acct.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-rad-acct.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-rad-acct.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});

//admin.auth.user-history.index

Breadcrumbs::for('admin.auth.user-history.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-history.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-history.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-history.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});


//admin.auth.user-payments-check.index

Breadcrumbs::for('admin.auth.user-payments-check.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-check.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-check.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-check.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});

//admin.auth.user-payments-logs.index

Breadcrumbs::for('admin.auth.user-payments-logs.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-logs.show', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-logs.edit', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});
Breadcrumbs::for('admin.auth.user-payments-logs.create', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
});


//admin.service-providers.index

Breadcrumbs::for('admin.service-providers.index', function ($trail) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('admin.service-providers.index'));
});


// VPN Servers
//admin.v-p-n-servers.edit

//Breadcrumbs::for('admin.v-p-n-servers.index', function ($trail, $id) {
//    $trail->parent('admin.auth.user.index');
//    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
//});
//Breadcrumbs::for('admin.v-p-n-servers.show', function ($trail, $id) {
//    $trail->parent('admin.auth.user.index');
//    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
//});
//Breadcrumbs::for('admin.v-p-n-servers.edit', function ($trail, $id) {
//    $trail->parent('admin.auth.user.index');
//    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
//});
//Breadcrumbs::for('admin.v-p-n-servers.create', function ($trail, $id) {
//    $trail->parent('admin.auth.user.index');
//    $trail->push(__('menus.backend.access.users.edit'), route('admin.auth.user.edit', $id));
//});


Breadcrumbs::for('admin.admin.intercom-marketing-data.datag', function ($trail) {
    $trail->push('Title Here', route('admin.admin.intercom-marketing-data.datag'));
});
Breadcrumbs::for('s-m-t-p2-g-o-email-data.index', function ($trail) {
    $trail->push('Title Here', route('s-m-t-p2-g-o-email-data.index'));
});


Breadcrumbs::for('admin.admin.reports.app-signups', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.app-signups'));
});


Breadcrumbs::for('appSignupReports.index', function ($trail) {
    $trail->push('Title Here', route('appSignupReports.index'));
});


Breadcrumbs::for('admin.auth.user-email-logs.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('admin.auth.user-email-logs.index'), route('admin.auth.user-email-logs.index', $id));
});
Breadcrumbs::for('admin.auth.user-conn-log.index', function ($trail, $id) {
    $trail->parent('admin.auth.user.index');
    $trail->push(__('admin.auth.user-conn-log.index'), route('admin.auth.user-conn-log.index', $id));
});





Breadcrumbs::for('admin.admin.reports.ltv-report', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-report'));
});

Breadcrumbs::for('admin.admin.reports.ltv-subscription-report', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-subscription-report'));
});
Breadcrumbs::for('admin.admin.reports.ltv-country-report', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-country-report'));
});
Breadcrumbs::for('admin.admin.reports.ltv-country-report-all-time', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-country-report-all-time'));
});


Breadcrumbs::for('admin.admin.reports.ltv-country-report-all-time-store', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-country-report-all-time-store'));
});

Breadcrumbs::for('admin.admin.reports.ltv-country-report-all-time-intercom-cross', function ($trail) {
    $trail->push('Title Here', route('admin.admin.reports.ltv-country-report-all-time-intercom-cross'));
});
Breadcrumbs::for('admin.service-providers.show', function ($trail) {
    $trail->push('Title Here', route('admin.service-providers.show'));
});

Breadcrumbs::for('admin.services.index', function ($trail) {
    $trail->push('Title Here', route('admin.services.index'));
});
Breadcrumbs::for('admin.services.create', function ($trail) {
    $trail->push('Title Here', route('admin.services.create'));
});

Breadcrumbs::for('admin.protocols.index', function ($trail) {
    $trail->push('Title Here', route('admin.protocols.index'));
});
Breadcrumbs::for('admin.services.show', function ($trail) {
    $trail->push('Title Here', route('admin.services.show'));
});

Breadcrumbs::for('admin.service-providers.edit', function ($trail) {
    $trail->push('Title Here', route('admin.service-providers.edit'));
});


