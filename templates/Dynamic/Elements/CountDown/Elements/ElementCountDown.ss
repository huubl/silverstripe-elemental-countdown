<h3>$Title</h3>
<div id="countdown-$ID" class="countdown" data-end="$End" data-elapse="$Elapse" >
    <% if $ShowMonths %>
        <span class="months">0</span> Months
    <% end_if %>
    <span class="days">0</span> Days
    <span class="hours">0</span> Hours
    <span class="minutes">0</span> Minutes
    <% if $ShowSeconds %>
        <span class="seconds">0</span> Seconds
    <% end_if %>
</div>
<% require javascript('silverstripe/admin: thirdparty/jquery/jquery.js') %>
<% require javascript('dynamic/silverstripe-elemental-countdown: thirdparty/jquery.countdown-2.1.0/jquery.countdown.min.js') %>
<% require javascript('dynamic/silverstripe-elemental-countdown: client/dist/countdown.init.min.js') %>
