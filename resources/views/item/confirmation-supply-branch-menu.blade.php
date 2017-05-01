<a {{Route::currentRouteName() == 'get.item.pending.confirmations' ? "class=bold href=#" : "href=".route('get.item.pending.confirmations') }}>
    Pending</a> |
<a {{Route::currentRouteName() == 'get.item.approved.confirmations' ? "class=bold href=#" : "href=".route('get.item.approved.confirmations') }}>
    Approved</a> |
<a {{Route::currentRouteName() == 'get.item.rejected.confirmations' ? "class=bold href=#" : "href=".route('get.item.rejected.confirmations') }}>
    Rejected</a>
