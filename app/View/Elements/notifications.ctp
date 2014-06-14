<? if ($loggedPlayer !== null && isset($notificationsUnread)) :?>

<script type="text/javascript">
	$(function() {
		var opts = {
			'closeButton': true,
			'debug': false,
			'positionClass': 'toast-top-right',
			'onclick': null,
			'showDuration': '1000',
			'hideDuration': '1000',
			'timeOut': 0,
			'extendedTimeOut': '1000',
			'showEasing': 'swing',
			'hideEasing': 'linear',
			'showMethod': 'fadeIn',
			'hideMethod': 'fadeOut'
		};
		var notifications = <? echo json_encode($notificationsUnread); ?>;
		for (var i in notifications) {
			var notification = notifications[i];
			var type = notification.Notification['type'];
			var func = toastr[type];
			func(notification.Notification['text'], notification.Notification['title'], opts);
		}
	});
</script>

<?
	$this->Notifications->markAsRead($notificationsUnread); 
?>

<?endif;?>