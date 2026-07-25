<?php

return [

    /*
    | Send a "your subscription is about to end" reminder this many days before
    | the term ends. The subscriptions:process command does the sending.
    */
    'reminder_days' => (int) env('SUBSCRIPTION_REMINDER_DAYS', 3),

    /*
    | Extra days of access after the term ends before the subscription is
    | marked expired. 0 = expire the moment the term ends.
    */
    'grace_days' => (int) env('SUBSCRIPTION_GRACE_DAYS', 0),

];
